<template>
  <div v-if="can(`logs_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row class="pt-2 mt-5">
      <v-col cols="12" sm="8" md="2">
        <v-menu
          ref="from_menu"
          v-model="from_menu"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <div class="mb-1">From Date</div>
            <v-text-field
              :hide-details="!payload.from_date"
              outlined
              dense
              v-model="payload.from_date_txt"
              readonly
              v-bind="attrs"
              v-on="on"
              placeholder="Date"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="payload.from_date"
            no-title
            scrollable
            @change="searchIt"
          >
            <v-spacer></v-spacer>
            <v-btn class="blue-grey" small dark @click="from_menu = false">
              Cancel
            </v-btn>
            <v-btn class="blue-grey darken-3" small dark @click="searchIt">
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="12" sm="8" md="2">
        <div class="mb-1">To Date</div>
        <v-menu
          ref="to_menu"
          v-model="to_menu"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              :hide-details="!payload.to_date"
              outlined
              dense
              v-model="payload.to_date_txt"
              readonly
              v-bind="attrs"
              v-on="on"
              placeholder="Date"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="payload.to_date"
            no-title
            scrollable
            @change="searchIt"
          >
            <v-spacer></v-spacer>
            <v-btn class="blue-grey" small dark @click="to_menu = false">
              Cancel
            </v-btn>
            <v-btn class="blue-grey darken-3" small dark @click="searchIt">
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="12" sm="6" md="2">
        <div class="mb-1">User ID</div>
        <v-text-field
          @input="searchIt"
          v-model="payload.UserID"
          outlined
          dense
          placeholder="Search..."
        ></v-text-field>
      </v-col>
      <v-col cols="12" sm="6" md="2">
        <div class="mb-1">Device ID</div>
        <v-autocomplete
          outlined
          dense
          @change="searchIt"
          placeholder="Search..."
          v-model="payload.DeviceID"
          :items="devices"
          item-text="device_id"
          item-value="device_id"
        >
        </v-autocomplete>
      </v-col>

      <!-- <v-col cols="12" sm="6" md="2">
        <div class="mb-1"> &nbsp;</div>
        <v-btn class="primary" @click="searchIt">Submit</v-btn>
      </v-col> -->
    </v-row>
    <v-row>
      <v-col>
        <v-card class="mb-5" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <v-toolbar-title><span> Attendances Logs</span></v-toolbar-title>

            <a
              style="padding-left: 10px"
              title="Reload Page/Reset Form"
              @click="firstLoad"
              ><v-icon class="mx-1">mdi mdi-reload</v-icon></a
            >
          </v-toolbar>
          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            dense
            :headers="headers_table"
            :items="data"
            model-value="data.id"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [50, 100, 500, 1000],
            }"
            class="elevation-1"
          >
            <template v-slot:item.UserID="{ item }">
              <v-edit-dialog
                large
                save-text="Reset"
                cancel-text="Ok"
                style="margin-left: 4%"
                @cancel="getRecords()"
                @save="getRecords()"
                @open="datatable_open"
              >
                {{ item.UserID }}
                <template v-slot:input>
                  <v-text-field
                    v-model="datatable_search_textbox"
                    @input="getRecords('search_system_user_id', $event)"
                    label="Search System User Id"
                  ></v-text-field>
                </template>
              </v-edit-dialog>
            </template>

            <template v-slot:item.time="{ item }">
              <v-edit-dialog
                large
                save-text="Reset"
                cancel-text="Ok"
                style="margin-left: 4%"
                @cancel="getRecords()"
                @save="getRecords()"
                @open="datatable_open"
              >
                {{ item.LogTime }}
                <template v-slot:input>
                  <v-text-field
                    v-model="datatable_search_textbox"
                    label="Search Date and Time"
                    @input="getRecords('search_time', $event)"
                  ></v-text-field>
                </template>
              </v-edit-dialog>
            </template>
            <template v-slot:item.device.device_name="{ item }">
              <v-edit-dialog
                large
                save-text="Reset"
                cancel-text="Ok"
                style="margin-left: 4%"
                @cancel="getRecords()"
                @save="getRecords()"
                @open="datatable_open"
              >
                {{ item.device ? item.device.name : "---" }}
                <template v-slot:input>
                  <v-text-field
                    v-model="datatable_search_textbox"
                    @input="getRecords('search_device_name', $event)"
                    label="Search Device Name"
                  ></v-text-field>
                </template>
              </v-edit-dialog>
            </template>
            <template v-slot:item.device.device_id="{ item }">
              <v-edit-dialog
                large
                save-text="Reset"
                cancel-text="Ok"
                style="margin-left: 4%"
                @cancel="getRecords()"
                @save="getRecords()"
                @open="datatable_open"
              >
                {{ item.device ? item.device.device_id : "---" }}
                <template v-slot:input>
                  <v-text-field
                    v-model="datatable_search_textbox"
                    @input="getRecords('search_device_id', $event)"
                    label="Search Device ID"
                  ></v-text-field>
                </template>
              </v-edit-dialog>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <!-- <v-row class="mt-5">
      <v-col cols="12">
        <v-data-table v-model="ids" item-key="id" :headers="headers" :items="data" :server-items-length="total"
          :loading="loading" :options.sync="options" :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }"></v-data-table>
      </v-col>
    </v-row> -->
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    datatable_search_textbox: "",
    datatable_searchById: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

    Model: "Log",
    endpoint: "attendance_logs",

    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,

    payload: {},

    loading: true,

    date: null,
    menu: false,

    loading: false,
    time_menu: false,

    log_payload: {
      user_id: 41,
      device_id: "OX-8862021010100",
      date: null,
      time: null,
    },
    headers: [
      {
        text: "UserID",
        align: "center",
        sortable: false,
        value: "UserID",
      },
      { text: "DeviceID", align: "center", sortable: false, value: "DeviceID" },
      {
        text: "Device Name",
        align: "center",
        sortable: false,
        value: "device.name",
      },
      { text: "LogTime", align: "center", sortable: false, value: "LogTime" },
    ],
    ids: [],

    data: [],
    devices: [],
    total: 0,
    options: {},
    payloadOptions: {},
    errors: [],
    response: "",
    snackbar: false,
    headers_table: [
      {
        text: "E.ID",
        align: "left",
        sortable: true,
        key: "UserID",
        value: "UserID",
      },
      {
        text: "Log Time",
        align: "left",
        sortable: true,
        key: "time", //sorting
        value: "time", //edit purpose
      },
      {
        text: "Device Name",
        align: "left",
        sortable: true,
        value: "devicename",
        value: "device.device_name",
      },
      {
        text: "Device ID",
        align: "left",
        sortable: true,
        key: "deviceid",
        value: "device.device_id",
      },
    ],
  }),
  created() {
    this.firstLoad();
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    firstLoad() {
      this.loading = true;

      this.payload.from_date = this.getDate();
      this.payload.to_date = this.getDate();
      this.payload.from_date_txt = this.getDate();
      this.payload.to_date_txt = this.getDate();
      this.getDeviceList();
    },
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
      //this.datatable_search_textbox = '';
    },
    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list`, payload).then(({ data }) => {
        this.devices = data;
      });
    },
    getDate() {
      const date = new Date();
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const day = date.getDate().toString().padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    getRecords(filter_column = "", filter_value = "") {
      if (filter_value != "" && filter_value.length <= 2) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 3 Characters to filter ";
        this.loading = false;
        return false;
      }
      this.getDataFromApi(this.endpoint, filter_column, filter_value);
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.payloadOptions = {
        params: {
          per_page: this.options.itemsPerPage,
          company_id: this.$auth.user.company_id,
          ...this.payload,
        },
      };
      if (filter_column != "")
        this.payloadOptions.params[filter_column] = filter_value;
      this.loading = true;
      this.$axios
        .get(`${url}?page=${this.options.page}`, this.payloadOptions)
        .then(({ data }) => {
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
        });
    },
    searchIt() {
      this.payload.from_date_txt = this.payload.from_date;
      this.payload.to_date_txt = this.payload.to_date;

      this.getDataFromApi();
      this.from_menu = false;
      this.to_menu = false;
      return false;
      let UserID = this.payload.UserID;
      let DeviceID = this.payload.DeviceID;

      if (UserID && UserID.length == 0 && DeviceID && DeviceID.length == 0) {
        this.getDataFromApi();
      } else {
        this.getDataFromApi(
          `${this.endpoint}/search/${this.$auth.user.company_id}`
        );
      }
    },
  },
};
</script>
