<template>
  <div v-if="can(`attendance_report_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
    </div>
    <v-card elevation="0" v-if="can(`attendance_report_view`)">
      <v-toolbar class="background" dense flat>
        <span class="headline white--text"> {{ title }} Filters </span>
        <v-spacer></v-spacer>
        <v-tooltip top color="primary" v-if="can(`attendance_report_view`)">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              class="ma-0"
              x-small
              :ripple="false"
              text
              v-bind="attrs"
              v-on="on"
              @click="process_file(report_type)"
            >
              <v-icon class="white--text">mdi-printer-outline</v-icon>
            </v-btn>
          </template>
          <span>PRINT</span>
        </v-tooltip>

        <v-tooltip top color="primary" v-if="can(`attendance_report_view`)">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              x-small
              :ripple="false"
              text
              v-bind="attrs"
              v-on="on"
              @click="process_file(report_type + '_download_pdf')"
            >
              <v-icon class="white--text">mdi-download-outline</v-icon>
            </v-btn>
          </template>
          <span>DOWNLOAD</span>
        </v-tooltip>

        <v-tooltip top color="primary" v-if="can(`attendance_report_view`)">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              x-small
              :ripple="false"
              text
              v-bind="attrs"
              v-on="on"
              @click="process_file(report_type + '_download_csv')"
            >
              <v-icon class="white--text">mdi-file-outline</v-icon>
            </v-btn>
          </template>
          <span>CSV</span>
        </v-tooltip>
      </v-toolbar>

      <v-card-text class="py-3">
        <v-row>
          <v-col md="3">
            <div>Frequency</div>
            <v-autocomplete
              class="mt-2"
              @change="changeReportType(report_type)"
              outlined
              dense
              v-model="report_type"
              x-small
              :items="['Daily', 'Weekly', 'Monthly', 'Custom']"
              item-text="['Daily']"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
          <v-col md="3">
            Status
            <v-select
              @change="getDataFromApi(`report`)"
              class="mt-2"
              outlined
              dense
              v-model="payload.status"
              x-small
              :items="statuses"
              item-value="id"
              item-text="name"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col md="3">
            Departments
            <v-autocomplete
              @change="getScheduledEmployees"
              class="mt-2"
              outlined
              dense
              multiple
              v-model="payload.department_ids"
              x-small
              :items="departments"
              item-value="id"
              item-text="name"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
          <v-col md="3">
            Employee ID
            <v-autocomplete
              @change="getDataFromApi(`report`)"
              class="mt-2"
              outlined
              dense
              v-model="payload.employee_id"
              x-small
              :items="scheduled_employees"
              item-value="system_user_id"
              item-text="name_with_user_id"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>

          <v-col md="3" v-if="report_type == 'Daily'">
            <div>Date</div>
            <div class="text-left mt-2">
              <v-menu
                class="mt-2"
                ref="daily_menu"
                v-model="daily_menu"
                :close-on-content-click="false"
                :return-value.sync="daily_date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="payload.daily_date"
                    outlined
                    dense
                    v-model="payload.daily_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="payload.daily_date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="daily_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.daily_menu, payload.daily_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </div>
          </v-col>
          <v-col md="3" v-if="report_type !== 'Daily'">
            <div class="text-left">
              <v-menu
                ref="from_menu"
                v-model="from_menu"
                :close-on-content-click="false"
                :return-value.sync="from_date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <div class="mb-2">From Date</div>
                  <v-text-field
                    :hide-details="payload.from_date"
                    outlined
                    dense
                    v-model="payload.from_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="payload.from_date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="from_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.from_menu, payload.from_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </div>
          </v-col>
          <v-col md="3" v-if="report_type !== 'Daily'">
            <div class="mb-2">To Date</div>

            <div class="text-left">
              <v-menu
                ref="to_menu"
                v-model="to_menu"
                :close-on-content-click="false"
                :return-value.sync="to_date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="payload.to_date"
                    outlined
                    dense
                    v-model="payload.to_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.to_date"
                  :max="max_date"
                  no-title
                  scrollable
                >
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="to_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.to_menu, payload.to_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </div>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider></v-divider>
    </v-card>
    <v-card
      class="mb-5 mt-5"
      elevation="0"
      v-if="can(`attendance_report_view`)"
    >
      <v-toolbar class="background" dense dark flat>
        <v-toolbar-title>
          <span> {{ title }} </span>
        </v-toolbar-title>

        <!-- <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          title="Reload"
          x-small
          :ripple="false"
          text
          @click="clearFilters()"
        >
          <v-icon dark white>mdi-reload</v-icon>
        </v-btn>
        <!-- </template>
          <span>Reload</span>
        </v-tooltip> -->
        <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="attendancFilters = true"
                >
                  <v-icon dark white @click="attendancFilters = true"
                    >mdi-filter</v-icon
                  >
                </v-btn>
              </template>
              <span>Filter</span>
            </v-tooltip> -->
        <v-spacer></v-spacer>

        <!-- <v-tooltip top color="primary" v-if="can(`attendance_report_create`)">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          title="Generate Log"
          x-small
          :ripple="false"
          text
          @click="generateLogsDialog = true"
        >
          <v-icon class="" dark white>mdi-plus-circle</v-icon>
        </v-btn>
        <!--  </template>
          <span>Generate Log</span>
        </v-tooltip> -->

        <!-- <v-tooltip top color="primary" v-if="can(`attendance_report_create`)">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          x-small
          :ripple="false"
          text
          title="Render Report"
          @click="reportSync = true"
        >
          <v-icon class="" dark color="white">mdi-cached</v-icon>
        </v-btn>
        <!-- </template>
          <span>Render Report</span>
        </v-tooltip> -->
      </v-toolbar>
      <v-data-table
        dense
        :headers="headers"
        :items="data"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-1"
        model-value="data.id"
        :server-items-length="totalRowsCount"
      >
        <template v-slot:header="{ props: { headers } }">
          <tr v-if="isFilter">
            <td
              style="width: 40px"
              v-for="header in headers"
              :key="header.text"
              class="table-search-header"
            >
              <v-text-field
                style="padding-left: 10px"
                v-if="header.filterable"
                v-model="filters[header.value]"
                id="header.value"
                @input="applyFilters(header.value, $event)"
                outlined
                height="10px"
                clearable
                autocomplete="off"
              ></v-text-field>

              <template v-else>
                <v-text-field
                  style="display: none"
                  outlined
                  height="10px"
                  clearable
                  autocomplete="off"
                ></v-text-field>
              </template>
            </td>
          </tr>
        </template>
        <template v-slot:item.date="{ item }">
          {{ item.date }}
        </template>
        <template v-slot:item.employee_id="{ item }">
          {{ item.employee_id }}
        </template>
        <template v-slot:item.employee_name="{ item }">
          {{ item.employee.first_name }} {{ item.employee.last_name }}
        </template>
        <template v-slot:item.status="{ item }">
          <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }">
              {{ setStatusLabel(item.status) }}
              <v-btn
                v-if="item.is_manual_entry"
                color="primary"
                text
                v-bind="attrs"
                v-on="on"
              >
                (ME)
              </v-btn>
            </template>
            <div>Reason: {{ item.last_reason?.reason }}</div>
            <div>Added By: {{ item.last_reason?.user?.email }}</div>
            <div>Created At: {{ item.last_reason?.created_at }}</div>
          </v-tooltip>
        </template>

        <template v-slot:item.shift="{ item }">
          <v-tooltip v-if="item && item.shift" top color="primary">
            <template v-slot:activator="{ on, attrs }">
              <div class="primary--text" v-bind="attrs" v-on="on">
                {{ (item.shift && item.shift.name) || "---" }}
              </div>
            </template>
            <div v-for="(iterable, index) in item.shift" :key="index">
              <span v-if="index !== 'id'">
                {{ caps(index) }}: {{ iterable || "---" }}</span
              >
            </div>
          </v-tooltip>
          <span v-else>---</span>
        </template>

        <template v-slot:item.device_in="{ item }">
          <v-tooltip v-if="item && item.device_in" top color="primary">
            <template v-slot:activator="{ on, attrs }">
              <div class="primary--text" v-bind="attrs" v-on="on">
                {{ (item.device_in && item.device_in.short_name) || "---" }}
              </div>
            </template>
            <div v-for="(iterable, index) in item.device_in" :key="index">
              <span v-if="index !== 'id'">
                {{ caps(index) }}: {{ iterable || "---" }}</span
              >
            </div>
          </v-tooltip>
          <span v-else>---</span>
        </template>

        <template v-slot:item.device_out="{ item }">
          <v-tooltip v-if="item && item.device_out" top color="primary">
            <template v-slot:activator="{ on, attrs }">
              <div class="primary--text" v-bind="attrs" v-on="on">
                {{ (item.device_out && item.device_out.short_name) || "---" }}
              </div>
            </template>
            <div v-for="(iterable, index) in item.device_out" :key="index">
              <span v-if="index !== 'id'">
                {{ caps(index) }}: {{ iterable || "---" }}</span
              >
            </div>
          </v-tooltip>
          <span v-else>---</span>
        </template>

        <template v-slot:item.actions="{ item }">
          <v-icon @click="editItem(item)" x-small color="primary" class="mr-2">
            mdi-pencil
          </v-icon>
          <v-icon
            @click="viewItem(item)"
            x-small
            color="primary"
            class="mr-2"
            v-if="can('attendance_report_view')"
          >
            mdi-eye
          </v-icon>
        </template>
      </v-data-table>
    </v-card>

    <v-row justify="center">
      <v-dialog persistent v-model="time_table_dialog" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5">Time Slots</span>
          </v-toolbar>
          <v-card-text>
            <ol class="pa-3">
              <li v-for="(shift, index) in shifts" :key="index">
                {{ (shift && shift.name) || "---" }}
                {{
                  shift.on_duty_time
                    ? `(${shift.on_duty_time} - ${shift.off_duty_time})`
                    : ""
                }}
              </li>
            </ol>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>

    <v-row justify="center">
      <v-dialog persistent v-model="dialog" max-width="700px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline"> Update Log </span>
          </v-card-title>
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
          <v-card-text>
            <v-container>
              <v-row>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-col md="12">
                    <v-menu
                      ref="time_menu_ref"
                      v-model="time_menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      :return-value.sync="payload.time"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="290px"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="editItems.time"
                          label="Time"
                          readonly
                          v-bind="attrs"
                          :rules="timeRules"
                          v-on="on"
                        ></v-text-field>
                      </template>
                      <v-time-picker
                        v-if="time_menu"
                        v-model="editItems.time"
                        full-width
                        format="24hr"
                      >
                        <v-spacer></v-spacer>
                        <v-btn
                          x-small
                          color="primary"
                          @click="time_menu = false"
                        >
                          Cancel
                        </v-btn>
                        <v-btn
                          x-small
                          color="primary"
                          @click="$refs.time_menu_ref.save(editItems.time)"
                        >
                          OK
                        </v-btn>
                      </v-time-picker>
                    </v-menu>
                    <span
                      v-if="errors && errors.time"
                      class="text-danger mt-2"
                      >{{ errors.time[0] }}</span
                    >
                  </v-col>
                  <!-- <v-col md="12">
                  <v-text-field
                    v-model="editItems.device_id"
                    label="Device Id"
                    readonly
                  ></v-text-field>
                  <span
                    v-if="errors && errors.device_id"
                    class="text-danger mt-2"
                    >{{ errors.device_id[0] }}</span
                  >
                </v-col> -->

                  <v-col md="12">
                    <v-autocomplete
                      label="Select Device"
                      v-model="editItems.device_id"
                      :items="devices"
                      item-text="name"
                      item-value="device_id"
                      :rules="deviceRules"
                    >
                    </v-autocomplete>
                    <span
                      v-if="errors && errors.device_id"
                      class="text-danger mt-2"
                      >{{ errors.device_id[0] }}</span
                    >
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      filled
                      label="Reason"
                      v-model="editItems.reason"
                      auto-grow
                      :rules="nameRules"
                      required
                    ></v-textarea>
                    <span v-if="errors && errors.reason" class="error--text">
                      {{ errors.reason[0] }}
                    </span>
                  </v-col>
                </v-form>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close"> Cancel </v-btn>
            <v-btn class="primary" small @click="update">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>

    <v-row justify="center">
      <v-dialog persistent v-model="reportSync" max-width="700px">
        <v-card>
          <v-card-title class="background">
            <span class="headline white--text"> Render Report </span>
            <v-spacer></v-spacer>
            <v-icon class="gray" dark @click="reportSync = false"
              >mdi-close-box</v-icon
            >
          </v-card-title>
          <RenderAttendance
            :endpoint="render_endpoint"
            @update-data-table="getDataFromApi()"
          />
        </v-card>
      </v-dialog>
    </v-row>

    <v-row justify="center">
      <v-dialog persistent v-model="generateLogsDialog" max-width="700px">
        <v-card>
          <v-card-title class="background">
            <span class="headline white--text"> Generate Log </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="generateLogsDialog = false"
              >mdi-close-box</v-icon
            >
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <GenerateLog
                  :endpoint="render_endpoint"
                  @update-data-table="getDataFromApi()"
                />
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>

    <v-dialog persistent v-model="add_manual_log" width="700">
      <v-card>
        <v-card-title class="text-h5 primary white--text darken-2" dark>
          Manual Log
        </v-card-title>

        <v-card-text class="pa-3">
          <v-row>
            <v-col md="12">
              <v-text-field
                v-model="log_payload.user_id"
                label="User Id"
              ></v-text-field>
              <span v-if="errors && errors.user_id" class="text-danger mt-2">{{
                errors.user_id[0]
              }}</span>
            </v-col>
            <v-col md="12">
              <v-autocomplete
                label="Select Device"
                v-model="log_payload.device_id"
                :items="devices"
                item-text="name"
                item-value="id"
                :rules="deviceRules"
              >
              </v-autocomplete>
              <span
                v-if="errors && errors.device_id"
                class="text-danger mt-2"
                >{{ errors.device_id[0] }}</span
              >
            </v-col>
            <v-col md="12">
              <v-autocomplete
                label="In/Out"
                v-model="log_payload.log_type"
                :items="['In', 'Out']"
                :rules="deviceRules"
              >
                {{ log_payload.log_type }}
              </v-autocomplete>
              <span v-if="errors && errors.log_type" class="text-danger mt-2">{{
                errors.log_type[0]
              }}</span>
            </v-col>
            <v-col cols="12" md="6">
              <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="log_payload.date"
                    label="Date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  >
                  </v-text-field>
                </template>
                <v-date-picker v-model="log_payload.date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.menu.save(log_payload.date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6">
              <v-menu
                ref="manual_time_menu_ref"
                v-model="manual_time_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="log_payload.time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-height="320px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="log_payload.time"
                    label="Time"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  >
                  </v-text-field>
                </template>
                <v-time-picker
                  v-if="manual_time_menu"
                  v-model="log_payload.time"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn x-small color="primary" @click="manual_ = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.manual_time_menu_ref.save(log_payload.time)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span v-if="errors && errors.time" class="text-danger mt-2">{{
                errors.time[0]
              }}</span>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            small
            :loading="loading"
            color="primary"
            @click="store_schedule"
          >
            Submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row justify="center">
      <v-dialog persistent v-model="log_details" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5 pa-2">Log Details</span>
            <v-spacer></v-spacer>
            Total logs
            <b class="background--text mx-1">({{ log_list.length }})</b>
          </v-toolbar>
          <v-card-text>
            <div class="pt-5">
              <span v-for="(log, index) in log_list" :key="index">
                {{ log.date }} - {{ log.time }}
                <hr />
              </span>
            </div>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  props: [
    "title",
    "shift_type_id",
    "headers",
    "render_endpoint",
    "process_file_endpoint",
  ],

  data: () => ({
    filters: {},
    attendancFilters: false,
    isFilter: false,
    totalRowsCount: 0,
    datatable_search_textbox: "",
    datatable_filter_date: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    date: null,
    menu: false,
    selectedItems: [],
    time_table_dialog: false,
    log_details: false,
    overtime: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    manual_time_menu: false,
    Model: "Visitor  Reports",
    endpoint: "report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    generateLogsDialog: false,
    reportSync: false,
    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,
    ids: [],
    departments: [],
    scheduled_employees: [],
    DateRange: true,
    devices: [],
    valid: true,
    nameRules: [(v) => !!v || "reason is required"],
    timeRules: [(v) => !!v || "time is required"],
    deviceRules: [(v) => !!v || "device is required"],
    main_report_type: "Multi In/Out Report",
    daily_menu: false,
    daily_date: null,
    dailyDate: false,
    editItems: {
      attendance_logs_id: "",
      UserID: "",
      device_id: "",
      user_id: "",
      reason: "",
      date: "",
      time: null,
      manual_entry: false,
    },
    loading: false,
    total: 0,

    report_type: "Monthly",
    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      employee_id: "",
      department_ids: [],
      status: "-1",
      late_early: "Select All",
    },
    log_payload: {
      user_id: null,
      device_id: "",
      date: null,
      time: null,
    },
    log_list: [],
    snackbar: false,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    shifts: [],
    errors: [],
    custom_options: {},
    statuses: [
      {
        name: `Select All`,
        id: `-1`,
      },
      {
        name: `Present`,
        id: `P`,
      },
      {
        name: `Absent`,
        id: `A`,
      },
      {
        name: `Missing`,
        id: `M`,
      },
      {
        name: `Off`,
        id: `O`,
      },
      {
        name: `Leave`,
        id: `L`,
      },
      {
        name: `Holiday`,
        id: `H`,
      },
      {
        name: `Vaccation`,
        id: `V`,
      },
      {
        name: `Manual Entry`,
        id: `ME`,
      },
    ],
    max_date: null,
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    main_report_type() {
      //this.getDataFromApi();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  async created() {
    this.main_report_type = this.main_report_type_props;
    this.loading = true;
    // this.setMonthlyDateRange();
    this.payload.daily_date = new Date().toJSON().slice(0, 10);

    this.payload.department_ids = this.$auth.user.assignedDepartments;

    let options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
        //department_ids: this.$auth.user.assignedDepartments,
      },
    };
    this.getDepartments(options);
    this.getDeviceList(options);

    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    let dd = new Date(dt.getFullYear(), m, 0);

    m = m < 10 ? "0" + m : m;

    this.payload.from_date = `${y}-${m}-01`;
    this.payload.to_date = `${y}-${m}-${dd.getDate()}`;

    await this.getDataFromApi();
  },

  methods: {
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },

    setSevenDays(selected_date) {
      const date = new Date(selected_date);

      date.setDate(date.getDate() + 6);

      let datetime = new Date(date);

      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();

      this.max_date = `${y}-${m}-${d}`;
      this.payload.to_date = `${y}-${m}-${d}`;
    },

    setThirtyDays(selected_date) {
      const date = new Date(selected_date);

      date.setDate(date.getDate() + 29);

      let datetime = new Date(date);

      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();

      this.max_date = `${y}-${m}-${d}`;
      this.payload.to_date = `${y}-${m}-${d}`;
    },

    set_date_save(from_menu, field) {
      from_menu.save(field);

      if (this.report_type == "Weekly") {
        this.setSevenDays(this.payload.from_date);
      } else if (
        this.report_type == "Monthly" ||
        this.report_type == "Custom"
      ) {
        this.setThirtyDays(this.payload.from_date);
      }

      this.getDataFromApi();
    },
    setFromDate() {
      if (this.payload.from_date == null) {
        const dt = new Date();
        const y = dt.getFullYear();
        const m = dt.getMonth() + 1;
        const formattedMonth = m < 10 ? "0" + m : m;
        this.payload.from_date = `${y}-${formattedMonth}-01`;
      }
    },
    changeReportType(report_type) {
      this.setFromDate();

      switch (report_type) {
        case "Daily":
          this.setDailyDate();
          break;
        case "Weekly":
          this.setSevenDays(this.payload.from_date);
          break;
        case "Monthly":
        case "Custom":
          this.setThirtyDays(this.payload.from_date);
          break;

        default:
          this.max_date = null;
          break;
      }

      this.getDataFromApi();
    },

    getDeviceList(options) {
      this.$axios.get(`/device_list`, options).then(({ data }) => {
        this.devices = data;
      });
    },

    setDailyDate() {
      this.payload.daily_date = new Date().toJSON().slice(0, 10);
      delete this.payload.from_date;
      delete this.payload.to_date;
    },

    store_schedule() {
      let { user_id, date, time, device_id } = this.log_payload;
      let log_payload = {
        UserID: user_id,
        LogTime: date + " " + time,
        DeviceID: device_id,
        company_id: this.$auth.user.company_id,
      };
      this.loading = true;

      this.$axios
        .post(`/generate_log`, log_payload)
        .then(({ data }) => {
          this.getDataFromApi();
          this.add_manual_log = false;
          this.loading = false;
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    setEmployeeId(id) {
      this.$store.commit("employee_id", id);
    },
    get_time_slots() {
      this.getShift(this.custom_options);
    },
    getShift(options) {
      this.$axios.get(`/shift`, options).then(({ data }) => {
        this.shifts = data.data.map((e) => ({
          name: e.name,
          on_duty_time: (e.time_table && e.time_table.on_duty_time) || "",
          off_duty_time: (e.time_table && e.time_table.off_duty_time) || "",
        }));
        this.time_table_dialog = true;
      });
    },

    getScheduledEmployees() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          department_ids: this.payload.department_ids,
          shift_type_id: this.shift_type_id,
        },
      };

      this.$axios
        .get(`/scheduled_employees_with_type`, options)
        .then(({ data }) => {
          this.scheduled_employees = data;
          this.scheduled_employees.unshift({
            system_user_id: "",
            name_with_user_id: "Select All",
          });
          this.getDataFromApi();
        });
    },
    // getDevices(options) {
    //   this.$axios.get(`/device`, options).then(({ data }) => {
    //     this.devices = data.data;
    //   });
    // },
    async getDepartments(options) {
      const { employee, user_type } = this.$auth.user;

      let url = "departments";

      try {
        if (user_type === "employee") {
          const id = employee.id;
          url = "assigned-department-employee";
          const { data } = await this.$axios.get(`${url}/${id}`, options);
          this.departments = data;
        } else {
          const { data } = await this.$axios.get(url, options);
          this.departments = data.data;
          // this.payload.department_ids = [data.data[0].id];
        }
      } catch (error) {
        console.error("Error fetching departments:", error);
      }
    },

    caps(str) {
      return str.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
    },

    can(per) {
      return this.$pagePermission.can(per, this);
    },

    applyFilters(name, value) {
      if (value && value.length < 2) return false;

      this.getDataFromApi();
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};
      this.isFilter = false;
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.loading = true;

      let late_early = this.payload.late_early;

      switch (late_early) {
        case "Select All":
          late_early = "SA";
          break;

        default:
          late_early = late_early.charAt(0);
          break;
      }

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          report_type: this.report_type,
          shift_type_id: this.shift_type_id,
          status: this.payload.status,
          late_early,
          overtime: this.overtime ? 1 : 0,
          ...this.filters,
          ...this.payload,
        },
      };
      if (filter_column != "") options.params[filter_column] = filter_value;

      this.$axios.get(url, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }

        this.data = data.data;
        this.total = data.total;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },

    editItem(item) {
      this.dialog = true;
      this.editItems = item;
      this.editItems.UserID = item.employee_id;
      this.editItems.date = item.edit_date;
    },

    update() {
      if (this.$refs.form.validate()) {
        let payload = {
          UserID: this.editItems.UserID,
          LogTime: this.editItems.date + " " + this.editItems.time,
          DeviceID: this.editItems.device_id,
          user_id: this.editItems.UserID,
          company_id: this.$auth.user.company_id,
        };
        this.$axios
          .post("/generate_manual_log", payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;
              // this.msg = data.message;
            } else {
              this.snackbar = true;
              this.response = data.message;
              this.renderByType(this.render_endpoint);
              this.close();
              this.editItems = [];
            }
          })
          .catch((e) => console.log(e));
      }
    },

    renderByType(type) {
      const UserID = this.editItems.UserID;
      const date = this.editItems.date;

      if (!UserID || !date) {
        alert("System User Id and Date field is required");
        return;
      }

      let payload = {
        params: {
          date: this.editItems.date,
          UserID: this.editItems.UserID,
          updated_by: this.$auth.user.id,
          company_id: this.$auth.user.company_id,
          manual_entry: true,
          reason: this.editItems.reason,
        },
      };

      this.$axios
        .get("/" + type, payload)
        .then(({ data }) => {
          this.loading = false;
          this.snackbar = true;
          this.response = data.message;
          this.getDataFromApi();
        })
        .catch((e) => console.log(e));
    },

    viewItem(item) {
      this.log_list = [];
      let options = {
        params: {
          per_page: 500,
          UserID: item.employee_id,
          LogTime: item.edit_date,
          company_id: this.$auth.user.company_id,
        },
      };
      this.log_details = true;

      this.$axios.get("attendance_single_list", options).then(({ data }) => {
        this.log_list = data.data;
      });

      // this.editedIndex = this.data.indexOf(item);
      // this.editedItem = Object.assign({}, item);
      // this.dialog = true;
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    pdfDownload() {
      let path = process.env.BACKEND_URL + "/pdf";
      let pdf = document.createElement("a");
      pdf.setAttribute("href", path);
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },

    process_file(type) {
      if (this.data && !this.data.length) {
        alert("No data found");
        return;
      }

      if (!this.payload.department_ids.length) {
        alert("Department Must be selected");
        return;
      }
      type = type.toLowerCase().replace("custom", "monthly");
      let path =
        process.env.BACKEND_URL +
        "/" +
        this.process_file_endpoint +
        type.toLowerCase();

      let qs = ``;

      qs += `${path}`;
      qs += `?main_shift_type=${this.shift_type_id}`;
      qs += `&shift_type_id=${this.shift_type_id}`;
      qs += `&company_id=${this.$auth.user.company_id}`;
      qs += `&status=${this.payload.status}`;
      if (
        this.payload.department_ids &&
        this.payload.department_ids.length > 0
      ) {
        qs += `&department_ids=${this.payload.department_ids.join(",")}`;
      }
      qs += `&employee_id=${this.payload.employee_id}`;
      qs += `&report_type=${this.report_type}`;

      if (this.report_type == "Daily") {
        qs += `&daily_date=${this.payload.daily_date}`;
      } else {
        qs += `&from_date=${this.payload.from_date}&to_date=${this.payload.to_date}`;
      }

      let report = document.createElement("a");
      report.setAttribute("href", qs);
      report.setAttribute("target", "_blank");
      report.click();

      this.getDataFromApi();
      return;
    },

    setStatusLabel(status) {
      const statuses = {
        A: "Absent",
        P: "Present",
        M: "Missing",
        O: "Week Off",
        L: "Leave",
        H: "Holiday",
        V: "Vaccation",
      };
      return statuses[status];
    },
  },
};
</script>
