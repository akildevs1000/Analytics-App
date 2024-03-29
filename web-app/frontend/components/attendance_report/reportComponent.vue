<template>
  <div v-if="can(`attendance_report_access`)">
    <div class="text-center">
      <v-snackbar
        v-model="snackbar"
        multi-line
        top="top"
        color="secondary"
        elevation="24"
      >
        <span v-html="response"></span>
      </v-snackbar>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
    </div>

    <v-card class="mb-5" elevation="0" v-if="can(`attendance_report_view`)">
      <v-toolbar class="backgrounds" dense flat>
        <v-toolbar-title> </v-toolbar-title>

        <v-spacer></v-spacer>

        <!--  <span style="padding-left: 15px"
          ><img
            v-if="donwload_pdf_file != ''"
            title="Download File"
            style="cursor: pointer"
            @click="view_report_pdf_file()"
            src="/icons/icon_print.png"
            class="iconsize"
        /></span>
        <span style="padding-left: 15px"
          ><img
            v-if="view_pdf_file != ''"
            title="View PDF File"
            style="cursor: pointer"
            @click="donwload_file()"
            src="/icons/icon_pdf.png"
            class="iconsize"
        /></span>
        <span style="padding-left: 15px">|||</span>
        <span style="padding-left: 15px"
          ><img
            title="Print"
            style="cursor: pointer"
            @click="process_file2(report_type)"
            src="/icons/icon_print.png"
            class="iconsize"
        /></span>
        <span style="padding-left: 15px"
          ><img
            title="Download Pdf"
            style="cursor: pointer"
            @click="process_file2(report_type + '_download_pdf')"
            src="/icons/icon_pdf.png"
            class="iconsize"
        /></span>
        <span style="padding-left: 15px">|||</span>-->
        <span style="padding-left: 15px"
          ><img
            title="Print"
            style="cursor: pointer"
            @click="process_file(report_type)"
            src="/icons/icon_print.png"
            class="iconsize"
        /></span>
        <span style="padding-left: 15px"
          ><img
            title="Download Pdf"
            style="cursor: pointer"
            @click="process_file(report_type + '_download_pdf')"
            src="/icons/icon_pdf.png"
            class="iconsize"
        /></span>
        <span style="padding-left: 15px"
          ><img
            title="Download Excel"
            style="cursor: pointer"
            @click="process_file(report_type + '_download_csv')"
            src="/icons/icon_excel.png"
            class="iconsize"
        /></span>

        <v-menu bottom right>
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon color="violet">mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="200" dense>
            <v-list-item
              v-if="can(`attendance_report_re_generate`)"
              @click="
                reportSync = true;
                key = key + 1;
              "
            >
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small> mdi-cached </v-icon>
                Re-Generate Report
              </v-list-item-title>
            </v-list-item>
            <v-list-item
              v-if="can(`attendance_report_manual_entry_access`)"
              @click="generateLogsDialog = true"
            >
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small>
                  mdi-plus-circle-outline
                </v-icon>
                Manual Log
              </v-list-item-title>
            </v-list-item>
            <!-- <v-list-item
              v-if="can(`attendance_report_manual_entry_access`)"
              @click="generateMultiLogsDialog = true"
            >
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small>
                  mdi-plus-circle-outline
                </v-icon>
                Manual Multi Log
              </v-list-item-title>
            </v-list-item> -->
            <v-list-item @click="process_file(report_type)">
              <v-list-item-title style="cursor: pointer">
                <img src="/icons/icon_print.png" class="iconsize" />
                Print
              </v-list-item-title>
            </v-list-item>
            <v-list-item @click="process_file(report_type + '_download_pdf')">
              <v-list-item-title style="cursor: pointer">
                <img src="/icons/icon_pdf.png" class="iconsize" />
                PDF
              </v-list-item-title>
            </v-list-item>

            <v-list-item @click="process_file(report_type + '_download_csv')">
              <v-list-item-title style="cursor: pointer">
                <img src="/icons/icon_excel.png" class="iconsize" />
                EXCEL
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-toolbar>

      <v-data-table
        dense
        :headers="headers"
        :items="data"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [31, 30, 50, 100, 500, 1000],
          page: true,
        }"
        class="elevation-1"
        model-value="data.id"
        :server-items-length="totalRowsCount"
        fixed-header
        :height="tableHeight"
        no-data-text="No Data available. Click 'Generate' button to see the results"
      >
        <template v-slot:item.employee_name="{ item }" style="padding: 0px">
          <v-row no-gutters :title="'Dep: ' + item.employee.department.name">
            <v-col
              md="2"
              style="
                padding: 3px;
                padding-left: 0px;
                width: 30px;
                max-width: 30px;
              "
            >
              <v-img
                style="
                  border-radius: 50%;
                  height: auto;
                  width: 30px;
                  max-width: 30px;
                "
                :src="
                  item.employee.profile_picture
                    ? item.employee.profile_picture
                    : '/no-profile-image.jpg'
                "
              >
              </v-img>
            </v-col>
            <v-col style="padding: 3px" md="8">
              <strong>
                {{
                  item.employee.first_name ? item.employee.first_name : "---"
                }}
                {{
                  item.employee.last_name ? item.employee.last_name : "---"
                }}</strong
              >
              <div class="secondary-value">
                {{
                  item.employee.employee_id
                    ? item.employee.employee_id
                    : item?.employee_id
                }}
              </div>
            </v-col>
          </v-row>
        </template>
        <template v-slot:item.status="{ item }">
          <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }">
              {{ setStatusLabel(item.status) }}
              <div class="secondary-value" v-if="item.status == 'P'">
                {{ getShortShiftDetails(item) }}
              </div>

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
          <div>
            {{ item.shift && item.shift.on_duty_time }} -
            {{ item.shift && item.shift.off_duty_time }}
          </div>
          <div class="secondary-value">
            {{ (item.shift && item.shift.name) || "---" }}
            <span v-if="checkHalfday(item || `---`)">
              {{ `(Half Day ${item.shift.halfday_working_hours} hrs)` }}
            </span>
          </div>
          <!-- <v-tooltip v-if="item && item.shift" top color="primary">
            <template v-slot:activator="{ on, attrs }">
              <div class="primary--text" v-bind="attrs" v-on="on">
                <div>
                  {{ item.shift.on_duty_time }} - {{ item.shift.off_duty_time }}
                </div>
                {{ (item.shift && item.shift.name) || "---" }}
              </div>
            </template>
            <div v-for="(iterable, index) in item.shift" :key="index">
              <span v-if="index !== 'id'">
                {{ caps(index) }}: {{ iterable || "---" }}</span
              >
            </div>
          </v-tooltip>
          <span v-else>---</span> -->
        </template>
        <!-- <template v-slot:item.name="{ item }">
          {{ item.employee.first_name }} {{ item.employee.last_name }}
        </template> -->
        <template v-slot:item.date="{ item }">
          <div>{{ item.date }}</div>
          <div class="secondary-value">
            {{ item.day }}
          </div>
        </template>
        <template v-slot:item.in="{ item }">
          <div>{{ item.in }}</div>
          <div class="secondary-value">
            <div
              v-if="
                item.device_in &&
                item.device_in.name &&
                item.device_in.name != '---'
              "
            >
              {{ item.device_in.name }}
            </div>
            <div v-else-if="item.device_id_in != '---'">
              {{ item.device_id_in }}
            </div>
            <div v-else>---</div>
          </div>
        </template>
        <template v-slot:item.out="{ item }">
          <div>{{ item.out }}</div>
          <div class="secondary-value">
            <div
              v-if="
                item.device_out &&
                item.device_out.name &&
                item.device_out.name != '---'
              "
            >
              {{ item.device_out.name }}
            </div>
            <div v-else-if="item.device_id_out != '---'">
              {{ item.device_id_out }}
            </div>
            <div v-else>---</div>

            <!-- {{ item.device_id_out == "Manual" ? "Manual" : "---" }} -->
          </div>
        </template>
        <template v-slot:item.in1="{ item }">
          <div>{{ item.in1 }}</div>
          <div class="secondary-value">
            {{ (item.device_in1 && item.device_in1) || "---" }}
          </div>
        </template>
        <template v-slot:item.out1="{ item }">
          <div>{{ item.out1 }}</div>
          <div class="secondary-value">
            {{ (item.device_out1 && item.device_out1) || "---" }}
          </div>
        </template>
        <template v-slot:item.in2="{ item }">
          <div>{{ item.in2 }}</div>
          <div class="secondary-value">
            {{ (item.device_in2 && item.device_in2) || "---" }}
          </div>
        </template>
        <template v-slot:item.out2="{ item }">
          <div>{{ item.out2 }}</div>
          <div class="secondary-value">
            {{ (item.device_out2 && item.device_out2) || "---" }}
          </div>
        </template>
        <template v-slot:item.in3="{ item }">
          <div>{{ item.in3 }}</div>
          <div class="secondary-value">
            {{ (item.device_in3 && item.device_in3) || "---" }}
          </div>
        </template>
        <template v-slot:item.out3="{ item }">
          <div>{{ item.out3 }}</div>
          <div class="secondary-value">
            {{ (item.device_out3 && item.device_out3) || "---" }}
          </div>
        </template>
        <template v-slot:item.in4="{ item }">
          <div>{{ item.in4 }}</div>
          <div class="secondary-value">
            {{ (item.device_in4 && item.device_in4) || "---" }}
          </div>
        </template>
        <template v-slot:item.out4="{ item }">
          <div>{{ item.out4 }}</div>
          <div class="secondary-value">
            {{ (item.device_out4 && item.device_out4) || "---" }}
          </div>
        </template>
        <template v-slot:item.in5="{ item }">
          <div>{{ item.in5 }}</div>
          <div class="secondary-value">
            {{ (item.device_in5 && item.device_in5) || "---" }}
          </div>
        </template>
        <template v-slot:item.out5="{ item }">
          <div>{{ item.out5 }}</div>
          <div class="secondary-value">
            {{ (item.device_out5 && item.device_out5) || "---" }}
          </div>
        </template>
        <template v-slot:item.in6="{ item }">
          <div>{{ item.in6 }}</div>
          <div class="secondary-value">
            {{ (item.device_in6 && item.device_in6) || "---" }}
          </div>
        </template>
        <template v-slot:item.out6="{ item }">
          <div>{{ item.out6 }}</div>
          <div class="secondary-value">
            {{ (item.device_out6 && item.device_out6) || "---" }}
          </div>
        </template>
        <template v-slot:item.in7="{ item }">
          <div>{{ item.in7 }}</div>
          <div class="secondary-value">
            {{ (item.device_in7 && item.device_in7) || "---" }}
          </div>
        </template>
        <template v-slot:item.out7="{ item }">
          <div>{{ item.out7 }}</div>
          <div class="secondary-value">
            {{ (item.device_out7 && item.device_out7) || "---" }}
          </div>
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
          <v-icon
            @click="editItem(item)"
            x-small
            color="primary"
            class="mr-2"
            v-if="can(`attendance_report_manual_entry_access`)"
          >
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
      <v-dialog persistent v-model="dialog" max-width="300px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline"> Employee Manual Log </span>
            <v-spacer></v-spacer>
            <v-icon @click="dialog = false" outlined dark>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-form
                  ref="form"
                  v-model="valid"
                  lazy-validation
                  style="width: 100%"
                >
                  <v-col md="12" class="pa-0">
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

                  <!-- <v-col md="12">
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
                  </v-col> -->
                  <v-col cols="12" class="pa-0">
                    <v-textarea
                      filled
                      label="Reason"
                      v-model="editItems.reason"
                      auto-grow
                      :rules="nameRules"
                      required
                      rows="2"
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
      <v-dialog persistent v-model="reportSync" max-width="800px">
        <v-card style="width: 100%">
          <v-card-title class="popup_background">
            <span class="headline"> Render Report </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="reportSync = false">mdi-close-box</v-icon>
          </v-card-title>
          <RenderAttendance
            :key="key"
            :shift_type_id="shift_type_id"
            :endpoint="render_endpoint"
            :display_emp_pic="display_emp_pic"
            :system_user_id="system_user_id"
            @update-data-table="getDataFromApi()"
          />
        </v-card>
      </v-dialog>
    </v-row>

    <v-row justify="center">
      <v-dialog persistent v-model="generateLogsDialog" max-width="800px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline">Employee Manual Log </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="generateLogsDialog = false"
              >mdi-close-box</v-icon
            >
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <GenerateLog
                  @close-popup="generateLogsDialog = false"
                  :endpoint="render_endpoint"
                  :system_user_id="system_user_id"
                  :shift_type_id="shift_type_id"
                  @update-data-table="getDataFromApi()"
                />
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-dialog persistent v-model="generateMultiLogsDialog" max-width="800px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline">Employee Manual Multi Log </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="generateLogsDialog = false"
              >mdi-close-box</v-icon
            >
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <GenerateLogMulti
                  @close-popup="generateLogsDialog = false"
                  :endpoint="render_endpoint"
                  :system_user_id="system_user_id"
                  :shift_type_id="shift_type_id"
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
        <v-card-title class="popup_background text-h5 darken-2" dark>
          Manual Log_old
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
      <v-dialog persistent v-model="log_details" max-width="800px">
        <v-card class="darken-1">
          <v-toolbar class="popup_background">
            <span class="text-h5 pa-2">Log Details</span>
            <v-spacer></v-spacer>

            <v-icon @click="log_details = false"
              >mdi-close-circle-outline</v-icon
            >
          </v-toolbar>
          <v-toolbar flat dense>
            Employee Id: <b>{{ log_list?.item?.employee?.system_user_id }}</b>
            <v-spacer></v-spacer>
            Total logs
            <b class="background--text">({{ log_list.length }})</b>
          </v-toolbar>
          <v-card-text>
            <!-- <hr /> -->
            <table class="short-table">
              <tr>
                <td>LogTime</td>
                <td>Device Id</td>
                <td>Device Function</td>
              </tr>
              <tr v-for="(log, index) in log_list" :key="index">
                <td>{{ log.LogTime }}</td>
                <td>{{ log.DeviceID }}</td>
                <td>
                  <b v-if="log.device.function == 'In'">{{
                    log?.device?.function
                  }}</b>
                  <b v-else-if="log.device.function == 'Out'">{{
                    log?.device?.function
                  }}</b>
                  <b v-else-if="log.device.function == 'auto'">{{
                    log?.device?.function
                  }}</b>
                  <b v-else>Unkown</b>
                </td>
              </tr>
            </table>
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
    "report_template",
    "branch_id",
    "title",
    "shift_type_id",
    "headers",
    "render_endpoint",
    "process_file_endpoint",
    // "report_type",
    "payload1",
    // "status",
    // "department_ids",
    // "employee_id",
    // "daily_date",
    // "from_date",
    // "to_date",
    "display_emp_pic",
    "system_user_id",
  ],

  data: () => ({
    donwload_pdf_file: "",
    view_pdf_file: "",

    key: 1,
    generateMultiLogsDialog: false,
    currentPage: "",
    tableHeight: 750,
    status: "",
    department_ids: "",
    employee_id: "",
    daily_date: "",
    from_date: "",
    to_date: "",
    report_type: "Monthly",

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
    options: { page: 1 },
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    manual_time_menu: false,
    Model: "Attendance Reports",
    endpoint: "report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    generateLogsDialog: false,
    reportSync: false,
    from_menu: false,
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
    dailyDate: false,
    editItems: {
      shift_type_id: 0,
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

    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      employee_id: "",
      department_ids: [],
      status: "-1",
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
        name: `Late In`,
        id: `LC`,
      },
      {
        name: `Early Out`,
        id: `EG`,
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
    originalTableHeaders: [],
    clearPagenumber: false,
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },

    // employees() {
    //   return this.$store.state.employeeList.map((e) => ({
    //     system_user_id: e.system_user_id,
    //     first_name: e.first_name,
    //     last_name: e.last_name,
    //     user_id: e.user_id,
    //     display_name: e.display_name,
    //     name_with_id: `${e.first_name} ${e.last_name}`,
    //     shift_type_id: e.latest_schedule && e.schedule_all.shift_type_id,
    //   }));
    // },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    // report_type(val) {
    //   this.changeReportType(val);
    // },
    // department_ids(value) {
    //   this.payload.department_ids = value;
    //   this.getDataFromApi();
    // },
    // employee_id(value) {
    //   this.payload.employee_id = value;
    //   this.getDataFromApi();
    // },
    // status(value) {
    //   this.payload.status = value;
    //   this.getDataFromApi();
    // },
    // daily_date(value) {
    //   this.payload.daily_date = value;
    //   this.getDataFromApi();
    // },
    // from_date(value) {
    //   this.payload.from_date = value;
    //   this.getDataFromApi();
    // },
    // to_date(value) {
    //   this.payload.to_date = value;
    //   this.getDataFromApi();
    // },

    payload1(value) {
      this.payload = value;
      // this.payload.status = value.status;
      // this.payload.daily_date = value.daily_date;
      // this.payload.from_date = value.from_date;
      // this.payload.to_date = value.to_date;
      this.report_type = value.report_type;
      this.department_ids = value.department_ids;
      this.employee_id = value.employee_id;
      this.status = value.status;

      if (this.payload.from_date == null) {
        this.payload.from_date = this.payload.daily_date;
        this.payload.to_date = this.payload.daily_date;
      }
      this.originalTableHeaders = this.headers;
      this.clearPagenumber = true;
      this.getDataFromApi();
    },

    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  mounted() {
    this.tableHeight = window.innerHeight - 370;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 370;
    });
  },
  async created() {
    // let data = [
    //   "[2023-11-21 13:42:26] Filo Shift. Log(s) have been rendered. Affected Ids: [1001] ",
    //   "[2023-11-21 13:42:26] Single Shift: No data found",
    //   "[2023-11-21 13:42:26] Dual Shift. Log(s) have been rendered. Affected Ids: [] 1001 has No Logs to render",
    //   "[2023-11-21 13:42:26] Multi Shift. Log(s) have been rendered. Affected Ids: [] 1001 : has No Logs to render",
    // ];
    // let message = "";
    // data.forEach((element) => {
    //   message = message + " \n  " + element + " \n  ";
    // });
    // this.snackbar = true;
    // this.response = data
    //   .map(
    //     (message) =>
    //       message.replace(/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] /, "") +
    //       "<br>"
    //   )
    //   .join("");
    // // this.loading = true;
    // // this.setMonthlyDateRange();
    // this.payload.daily_date = new Date().toJSON().slice(0, 10);
    // this.payload.department_ids = this.$auth.user.assignedDepartments;
    // let options = {
    //   params: {
    //     per_page: 1000,
    //     company_id: this.$auth.user.company_id,
    //     //department_ids: this.$auth.user.assignedDepartments,
    //   },
    // };
    // this.getDepartments(options);
    // this.getDeviceList(options);
    // let dt = new Date();
    // let y = dt.getFullYear();
    // let m = dt.getMonth() + 1;
    // let dd = new Date(dt.getFullYear(), m, 0);
    // m = m < 10 ? "0" + m : m;
    // this.payload.from_date = `${y}-${m}-01`;
    // this.payload.to_date = `${y}-${m}-${dd.getDate()}`;
    // // this.from_date = this.payload.daily_date;
    // // this.to_date = this.payload.daily_date;
    // // this.payload.from_date = this.payload.daily_date;
    // // this.payload.to_date = this.payload.daily_date;
  },

  methods: {
    checkHalfday(item) {
      let currentDay = new Date().toLocaleString("en-US", {
        weekday: "long",
      });

      return item.shift && currentDay === item.shift.halfday;
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
          this.generateLogsDialog = false;
          this.loading = false;
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    update() {
      //  UserID: this.editItems.UserID,
      //       LogTime: this.editItems.date + " " + this.editItems.time,
      //       DeviceID: this.editItems.device_id,
      //       user_id: this.editItems.UserID,
      //       company_id: this.$auth.user.company_id,
      //console.log(this.editItems);
      //console.log(this.employees);

      // let emp = this.employees.find(
      //   (e) => e.system_user_id == this.editItems.UserID
      // );

      //let { user_id, date, time } = this.log_payload;

      //console.log(emp);
      // let shift_type_id = this.editItems.shift_type_id;

      let log_payload = {
        UserID: this.editItems.UserID,
        LogTime: this.editItems.date + " " + this.editItems.time,
        DeviceID: "Manual",
        company_id: this.$auth.user.company_id,
        log_type: "auto",
      };
      this.loading = true;

      // if (!user_id || !date || !time) {
      //   alert("Please enter required fields");
      //   return;
      // }

      this.$axios
        .post(`/generate_log`, log_payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.render_report(
              this.editItems.date,
              this.editItems.shift_type_id
            );
            this.$emit("close-popup");
            this.snackbar = true;
            this.response = data.message;
            this.getDataFromApi();
            //this.generateLogsDialog = false;
            this.dialog = false;
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    render_report(date, shift_type_id) {
      let payload = {
        params: {
          dates: [date, date],
          UserIds: [this.editItems.UserID],
          company_ids: [this.$auth.user.company_id],
          user_id: this.$auth.user.id,
          updated_by: this.$auth.user.id,
          reason: this.reason,
          employee_ids: [this.editItems.UserID],
          shift_type_id: shift_type_id,
        },
      };
      this.$axios
        .get("render_logs", payload)
        .then(({ data }) => {
          // let message = "";
          // data.forEach((element) => {
          //   message = message + " \n \n  \n" + element;
          // });
          this.snackbar = true;
          let message = data
            .map(
              (message) =>
                message.replace(
                  /^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] /,
                  ""
                ) + "<br>"
            )
            .join("");
          let searchString = "No schedule is mapped with   date and employee";
          let found = data.includes(searchString);

          console.log(found);

          if (found) {
            message = searchString;
          }
          this.response = message;
          this.loading = false;
          this.$emit("update-data-table");
        })
        .catch((e) => console.log(e));
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
      this.options.page = 1;
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
      if (!this.payload.from_date) return false;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.loading = true;
      // if (this.clearPagenumber) {
      //   console.log("this.options", this.options);
      //   page = 1;
      //   this.options.page = 1;
      // }
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          report_type: this.report_type,
          shift_type_id: this.shift_type_id,
          overtime: this.overtime ? 1 : 0,
          ...this.filters,
          ...this.payload,
        },
      };

      // console.log(this.employee_id);
      // if (this.employee_id) {
      //   console.log("headers", this.headers);

      //   this.headers = this.originalTableHeaders.filter(
      //     (item) => item.value != "employee_name"
      //   );
      // } else {
      //   this.headers = this.originalTableHeaders;
      // }
      if (filter_column != "") options.params[filter_column] = filter_value;

      this.$axios.get(url, options, {}).then(({ data }) => {
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

        if (this.clearPagenumber) {
          this.options.page = 1;
          this.clearPagenumber = false;
        }

        //this.getAverageTimeCalculation(data);

        // try {
        //   if (this.shift_type_id == 1)
        //     this.$emit("genRecordCount", this.totalRowsCount);
        //   if (this.shift_type_id == 2)
        //     this.$emit("multiRecordCount", this.totalRowsCount);
        //   if (this.shift_type_id == 5)
        //     this.$emit("dualRecordCount", this.totalRowsCount);
        // } catch (e) {}
      });
    },

    editItem(item) {
      this.dialog = true;
      this.editItems = item;
      this.editItems.UserID = item.employee_id;
      this.editItems.date = item.edit_date;
    },

    // update() {
    //   if (this.$refs.form.validate()) {
    //     let payload = {
    //       UserID: this.editItems.UserID,
    //       LogTime: this.editItems.date + " " + this.editItems.time,
    //       DeviceID: this.editItems.device_id,
    //       user_id: this.editItems.UserID,
    //       company_id: this.$auth.user.company_id,
    //     };
    //     this.$axios
    //       .post("/generate_manual_log", payload)
    //       .then(({ data }) => {
    //         this.loading = false;
    //         if (!data.status) {
    //           this.errors = data.errors;
    //           // this.msg = data.message;
    //         } else {
    //           this.snackbar = true;
    //           this.response = data.message;
    //           this.renderByType(this.render_endpoint);
    //           this.close();
    //           this.editItems = [];
    //         }
    //       })
    //       .catch((e) => console.log(e));
    //   }
    // },

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
        this.log_list.item = item;
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

    donwload_file() {
      let path =
        process.env.BACKEND_URL +
        "/download_finalfile?file=" +
        this.donwload_pdf_file;

      let report = document.createElement("a");
      report.setAttribute("href", path);
      report.setAttribute("target", "_blank");
      report.click();

      return;
    },
    view_report_pdf_file() {
      let path =
        process.env.BACKEND_URL +
        "/view_finalfile?t=" +
        Math.random(10000, 99000) +
        "&file=" +
        this.view_pdf_file;

      let report = document.createElement("a");
      report.setAttribute("href", path);
      report.setAttribute("target", "_blank");
      report.click();

      return;
    },

    process_file2(type) {
      if (this.data && !this.data.length) {
        alert("No data found");
        return;
      }

      let path =
        process.env.BACKEND_URL +
        "/" +
        this.process_file_endpoint +
        type.toLowerCase() +
        "_merge_job";

      let qs = ``;

      qs += `${path}`;
      qs += `?report_template=${this.report_template}`;
      qs += `&main_shift_type=${this.shift_type_id}`;

      if (parseInt(this.payload.branch_id) > 0)
        qs += `&branch_id=${this.payload.branch_id}`;

      qs += `&shift_type_id=${this.shift_type_id}`;
      qs += `&company_id=${this.$auth.user.company_id}`;
      qs += `&status=${this.payload.status & this.payload.status || "-1"}`;
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
      console.log(qs);
      this.loading = true;
      this.donwload_pdf_file = "";
      this.view_pdf_file = "";
      this.snackbar = true;
      this.response = "Processing your request...Please wait ";
      alert(this.response);
      let options = {
        params: {},
      };
      this.$axios.get(qs, options).then(({ data }) => {
        console.log(data);
        this.donwload_pdf_file = data;
        this.view_pdf_file = data;
        this.verify_generated_pdf_file(data);
      });

      // let report = document.createElement("a");
      // report.setAttribute("href", qs);
      // report.setAttribute("target", "_blank");
      // report.click();

      // return;
    },

    verify_generated_pdf_file(data) {
      let qs =
        process.env.BACKEND_URL +
        "/verify_generated_pdf_file?file=" +
        this.donwload_pdf_file;

      let options = {
        params: {},
      };
      this.$axios.get(qs, options).then(({ data }) => {
        console.log(data);

        if (data == 1) {
          this.loading = false;

          this.snackbar = true;
          this.response =
            "Processing completed . Now you can Download your Report ";
        } else {
          this.loading = false;

          this.snackbar = true;
          this.response =
            "Processing Not completed . Wait for few more minutes";
        }
      });
    },

    process_file(type) {
      if (this.data && !this.data.length) {
        alert("No data found");
        return;
      }

      // if (this.payload.department_ids == undefined) {
      //   alert("Department Must be selected");
      //   return;
      // }

      // if (!this.payload.department_ids.length) {
      //   alert("Department Must be selected");
      //   return;
      // }
      //type = "monthly";
      //type = type.toLowerCase().replace("custom", "monthly");
      let path =
        process.env.BACKEND_URL +
        "/" +
        this.process_file_endpoint +
        type.toLowerCase();

      let qs = ``;

      qs += `${path}`;
      qs += `?report_template=${this.report_template}`;
      qs += `&main_shift_type=${this.shift_type_id}`;

      if (parseInt(this.payload.branch_id) > 0)
        qs += `&branch_id=${this.payload.branch_id}`;

      qs += `&shift_type_id=${this.shift_type_id}`;
      qs += `&company_id=${this.$auth.user.company_id}`;
      qs += `&status=${this.payload.status & this.payload.status || "-1"}`;
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
      console.log(qs);
      let report = document.createElement("a");
      report.setAttribute("href", qs);
      report.setAttribute("target", "_blank");
      report.click();

      //this.getDataFromApi();
      return;
    },
    getShortShiftDetails(item) {
      if (item.shift) {
        let shiftWorkingHours = item.shift.working_hours;
        let employeeHours = item.total_hrs;

        if (
          shiftWorkingHours != "" &&
          employeeHours != "" &&
          shiftWorkingHours != "---" &&
          employeeHours != "---"
        ) {
          let [hours, minutes] = shiftWorkingHours.split(":").map(Number);
          shiftWorkingHours = hours * 60 + minutes;

          [hours, minutes] = employeeHours.split(":").map(Number);
          employeeHours = hours * 60 + minutes;

          if (
            employeeHours < shiftWorkingHours &&
            !this.checkHalfday(item || `---`)
          ) {
            return "Short Shift";
          }
        }
      }
    },
    setStatusLabel(status) {
      const statuses = {
        A: "Absent",
        P: "Present",
        M: "Missing",
        LC: "Late In",
        EG: "Early Out",
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
<!-- <style scoped>
.v-slide-group__content {
  height: 30px !important;
}
</style> -->
<style scoped>
.short-table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
