<template>
  <div v-if="can(`employee_schedule_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog persistent v-model="dialog" max-width="1100">
      <v-card>
        <v-card-title dense class="popup_background">
          <span> {{ Module }}</span>
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text class="mt-2">
          <v-row>
            <v-col>
              <input
                style="padding-top: 8px"
                class="form-control"
                v-model="editedItem.timezone_name"
              />
              <span
                class="error--text"
                v-if="errors.timezone_name && errors.timezone_name[0]"
              >
                {{ errors.timezone_name[0] }}
              </span>
            </v-col>
            <v-col>
              <select
                @change="setDefault(editedItem.timezone_id)"
                class="form-select"
                v-model="editedItem.timezone_id"
                :error-messages="errors.timezone_id && errors.timezone_id[0]"
              >
                <option disabled selected>Timezone Id</option>
                <option v-for="n in 64" :key="n" :value="n">
                  Tz{{ n }}
                  <span v-if="n == 1">(24 Hrs)</span>
                  <span v-if="n == 64">Lock Timezone</span>
                </option>
              </select>
              <span
                class="error--text"
                v-if="errors.timezone_id && errors.timezone_id[0]"
              >
                {{ errors.timezone_id[0] }}
              </span>
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-text>
          <table style="width: 100%">
            <thead>
              <tr class="popup_background popup_title" dark>
                <th
                  class="text-center popup_title"
                  style="font-size: 17px !important"
                >
                  Time
                </th>
                <th
                  class="text-center popup_title"
                  colspan="2"
                  v-for="n in 4"
                  :key="n"
                  :value="n"
                  style="font-size: 17px !important"
                >
                  Interval {{ n }}
                </th>
              </tr>
              <tr>
                <th class="text-center">Weekday</th>
                <th class="text-center">begin Time</th>
                <th class="text-center">End Time</th>
                <th class="text-center">begin Time</th>
                <th class="text-center">End Time</th>
                <th class="text-center">begin Time</th>
                <th class="text-center">End Time</th>
                <th class="text-center">begin Time</th>
                <th class="text-center">End Time</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(d, index) in days" :key="index">
                <td>{{ d.name }}</td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval1']['begin']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval1']['end']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval2']['begin']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval2']['end']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval3']['begin']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval3']['end']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval4']['begin']"
                    type="time"
                  />
                </td>
                <td>
                  <input
                    v-model="editedItem.interval[d.index]['interval4']['end']"
                    type="time"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </v-card-text>

        <v-card-actions v-if="!readOnly">
          <v-btn small color="background white--text" @click="reset"
            >Reset</v-btn
          >
          <v-spacer></v-spacer>
          <v-btn small color="primary" @click="submit">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog persistent v-model="syncDeviceDialog" max-width="1100">
      <v-card>
        <v-card-title dense class="popup_background">
          <span class="popup_title">Sync Device</span>

          <v-spacer></v-spacer>
          <v-icon @click="syncDeviceDialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-progress-linear
            v-if="loading_devicesync"
            :active="loading_devicesync"
            :indeterminate="loading_devicesync"
            absolute
            color="primary"
          ></v-progress-linear>
          <table style="width: 100%" class="mt-2">
            <thead>
              <tr class=" " dark>
                <th style="width: 20%">Device ID</th>
                <th style="width: 70%">Message</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(d, index) in deviceResults" :key="index">
                <td>{{ d.DeviceID }}</td>
                <td v-html="d.message"></td>
                <td class="text-center">
                  <v-icon color="primary" v-if="d.status">mdi-check</v-icon>
                  <v-icon color="error" v-else>mdi-close</v-icon>
                </td>
              </tr>

              <tr v-if="deviceResults.length == 0">
                <td colspan="3" class="text-center">No Data   available</td>
              </tr>
            </tbody>
          </table>
          <!-- <br />
          <v-btn small color="grey white--text" @click="syncDeviceDialog = false">
            Close</v-btn> -->
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Module }}</h3>
        <div>Dashboard / {{ Module }}</div>
      </v-col>
      <v-col cols="6">
        <div class="text-right">
          <v-btn @click="openDeviceDialog" small color="primary" class="mb-2">
            Sync Device <v-icon class="mx-1">mdi-laptop</v-icon>
          </v-btn>
          <v-btn @click="dialog = true" small color="primary" class="mb-2">
            {{ Module }} <v-icon class="mx-1">mdi-plus</v-icon></v-btn>
        </div>
      </v-col>
    </v-row> -->
    <!-- <Back class="primary white--text" /> -->

    <v-card class="mt-2">
      <v-toolbar class="rounded-md" dense flat>
        <v-toolbar-title><span> Timezones List</span></v-toolbar-title>
        <!-- <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          text
          title="Reload"
        >
          <v-icon class="ml-2" @click="getDataFromApi()" dark
            >mdi mdi-reload</v-icon
          >
        </v-btn>
        <!-- </template>
          <span>Reload</span>
        </v-tooltip> -->

        <v-spacer></v-spacer>

        <!-- <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          x-small
          :ripple="false"
          text
          title="Sync To Devices"
          @click="openDeviceDialog"
        >
          <v-icon dark>mdi-laptop</v-icon>
        </v-btn>
        <!-- </template>
          <span>Sync To Devices</span>
        </v-tooltip> -->
        <!-- <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          x-small
          :ripple="false"
          text
          title="Add Timezone"
          @click="addItem"
        >
          <v-icon dark white>mdi-plus-circle</v-icon>
        </v-btn>
        <!-- </template>
          <span>Add New Timezone</span>
        </v-tooltip> -->
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
          itemsPerPageOptions: [100, 500, 1000],
        }"
        class="elevation-1"
      >
        <template v-slot:item.timezone_id="{ item }">
          {{ item.timezone_id }}
        </template>
        <template v-slot:item.timezone_name="{ item }">
          {{ item.timezone_name }}
        </template>
        <template v-slot:item.days="{ item }">
          <v-btn
            style="cursor: text"
            v-for="({ day, isScheduled }, idx) in item.scheduled_days"
            :key="idx"
            :class="isScheduled ? `circle-btn-green` : `circle-btn-grey`"
            class="mx-1"
            fab
            small
          >
            <span :class="isScheduled ? `primary--text` : `grey--text`">{{
              day
            }}</span>
          </v-btn>
        </template>
        <template v-slot:item.menu="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list width="120" dense>
              <v-list-item @click="viewItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-eye </v-icon>
                  View
                </v-list-item-title>
              </v-list-item>
              <v-list-item @click="editItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-pencil </v-icon>
                  Edit
                </v-list-item-title>
              </v-list-item>
              <v-list-item @click="deleteItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="error" small> mdi-delete </v-icon>
                  Delete
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
import Back from "../components/Snippets/Back.vue";

let days = [
  { index: "0", name: "Monday", short_name: "M" },
  { index: "1", name: "Tuesday", short_name: "T" },
  { index: "2", name: "Wednesday", short_name: "W" },
  { index: "3", name: "Thursday", short_name: "TH" },
  { index: "4", name: "Friday", short_name: "F" },
  { index: "5", name: "Saturday", short_name: "SA" },
  { index: "6", name: "Sunday", short_name: "SU" },
];
export default {
  components: { Back },
  data: () => ({
    snack: false,
    snackColor: "",
    snackText: "",
    datatable_search_textbox: "",

    pagination: {
      current: 1,
      total: 0,
      per_page: 100,
    },
    loading_devicesync: false,
    Module: "Timezone",
    options: {},
    endpoint: "timezone",
    search: "",
    snackbar: false,
    dialog: false,
    syncDeviceDialog: false,

    loading: false,
    loading_dialog: false,
    isEdit: false,
    total: 0,
    response: "",
    data: [],
    dayBoxes: [],
    errors: [],

    days,
    editedItem: {
      timezone_id: "",
      timezone_name: "Timzone Name",
      interval: [
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
      ],
    },
    defaultItem: {
      timezone_id: "",
      timezone_name: "Timzone Name",
      interval: [
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
        { interval1: {}, interval2: {}, interval3: {}, interval4: {} },
      ],
    },
    headers_table: [
      {
        text: "Timezone ID",
        align: "left",
        sortable: true,
        key: "timezone_id",
        value: "timezone_id",
      },
      {
        text: "Timezone Name",
        align: "left",
        sortable: true,
        key: "timezone_name",
        value: "timezone_name",
      },
      {
        text: "Days",
        align: "left",
        sortable: false,
        key: "days",
        value: "days",
      },
      {
        text: "Actions",
        align: "left",
        sortable: false,
        key: "menu",
        value: "menu",
      },
    ],
    deviceResults: [],
    readOnly: false,
    editedIndex: -1,
  }),

  computed: {},

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
    options_dialog: {
      handler() {
        this.getDataFromApiForDialog();
      },
      deep: true,
    },
    search() {
      this.pagination.current = 1;
      this.searchIt();
    },
  },
  created() {
    this.loading = true;
    this.loading_dialog = true;

    this.options = {
      params: {
        per_page: 100,
        company_id: this.$auth.user.company_id,
      },
    };
  },

  methods: {
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
    // processInput(index, interval, type, input) {
    //   this.editedItem.interval[index] = { "begin": interval,type, input };
    // },
    onPageChange() {
      this.getDataFromApi();
    },
    addItem() {
      this.dialog = true;
      this.readOnly = false;
      this.editedIndex = -1;
      this.editedItem = this.defaultItem;
    },
    viewItem(item) {
      this.dialog = true;
      this.readOnly = true;
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
    },
    editItem(item) {
      this.dialog = true;
      this.readOnly = false;
      this.editedIndex = this.data.indexOf(item);
      // let json = item.interval;
      // for (let day in json) {
      //   for (let interval in json[day]) {
      //     if (
      //       json[day][interval].hasOwnProperty("begin") &&
      //       json[day][interval].hasOwnProperty("end")
      //     ) {
      //       this.editedItem.interval[day] = json[day];
      //     } else {
      //       this.editedItem.interval[day] = json[day];
      //     }
      //   }
      // }

      this.editedItem = Object.assign({}, item);
    },
    showShortDays(days) {
      let arr = [];
      for (let day in days) {
        for (let interval in days[day]) {
          if (
            days[day][interval].hasOwnProperty("begin") &&
            days[day][interval].hasOwnProperty("end")
          ) {
            arr.push({
              day: this.days[day]["short_name"],
              dayWeek: this.days[day]["dayWeek"],
              isScheduled: true,
            });
            break;
          } else {
            arr.push({
              day: this.days[day]["short_name"],
              dayWeek: this.days[day]["dayWeek"],
              isScheduled: false,
            });
            break;
          }
        }
      }
      return arr;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    async openDeviceDialog() {
      if (!this.data.length) {
        this.snackbar = true;
        this.response = "No data found";
        return;
      }
      this.syncDeviceDialog = true;

      try {
        let endpoint = "getDevicesCountForTimezone";
        const { data } = await this.$axios.post(endpoint, this.editedItem);
        this.processTimeZone(data);
      } catch (error) {}
    },
    processTimeZone(devices) {
      this.deviceResults = [];
      let payload = {
        company_id: this.$auth.user.company_id,
      };
      let counter = 0;
      devices.forEach(async (DeviceID) => {
        try {
          this.loading_devicesync = true;
          let endpoint = `${DeviceID}/WriteTimeGroup`;
          const { data } = await this.$axios.post(endpoint, payload);
          let json = {
            DeviceID,
            message:
              '<span style="color:red">Device communication error</span>',
            status: false,
          };

          if (data.status == 200) {
            (json.message =
              '<span style="color:green">Timezone data has been upload'),
              (json.status = true);
            counter++;
          } else {
            counter++;
          }

          this.deviceResults.push(json);
          if (counter == devices.length) {
            this.loading_devicesync = false;
          }
        } catch (error) {}
      });
    },
    close() {
      this.dialog = false;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    getDataFromApi_DatatablFilter(filter_column, e) {
      this.getDataFromApi(`${this.endpoint}/search/${e}`, filter_column, e);
    },
    //main
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.loading = true;

      let page = this.pagination.current;

      let options = {
        params: {
          per_page: this.pagination.per_page,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };
      if (filter_column != "") {
        options.params["datatable_filter"] = true;
        options.params[filter_column] = filter_value;
      }

      this.$axios.get(url, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    searchIt() {
      let s = this.search.length;
      let search = this.search;
      if (s == 0) {
        this.getDataFromApi();
      } else if (s > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${search}`);
      }
    },
    setDefault(v) {
      if (v == 1) {
        this.days.forEach((e, i) => {
          this.editedItem.interval[e.index][`interval1`]["begin"] = "00:00";
          this.editedItem.interval[e.index][`interval1`]["end"] = "23:59";
        });
      }

      if (v == 64) {
        this.days.forEach((e, i) => {
          this.editedItem.interval[e.index][`interval1`]["begin"] = "00:00";
          this.editedItem.interval[e.index][`interval1`]["end"] = "00:00";
        });
      }
    },
    reset() {
      this.days.forEach((e) => {
        for (let j = 1; j <= 4; j++) {
          this.editedItem.interval[e.index][`interval${j}`] = {};
          this.editedItem.interval[e.index][`interval${j}`] = {};
        }
      });
    },
    submit() {
      let sortedDays = this.showShortDays(this.editedItem.interval);
      this.editedItem["scheduled_days"] = sortedDays;
      this.editedItem.company_id = this.$auth.user.company_id;

      return this.editedIndex === -1 ? this.store() : this.update();
    },
    store() {
      this.$axios
        .post(`/${this.endpoint}`, this.editedItem)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            return;
          }
          this.snackbar = data.status;
          this.response = data.message;
          this.dialog = false;
          this.getDataFromApi();
        })
        .catch((err) => {});
    },

    update() {
      this.$axios
        .put(`/${this.endpoint}/${this.editedItem.id}`, this.editedItem)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            return;
          }
          this.snackbar = data.status;
          this.response = data.message;
          this.dialog = false;
          this.getDataFromApi();
        })
        .catch((err) => {
          console.log(err.message);
        });
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`${this.endpoint}/` + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
            this.getDataFromApi();
          })
          .catch((err) => console.log(err));
    },
  },
};
</script>
<style scoped>
.circle-btn-grey {
  border-radius: 50%;
  border: 1px solid grey;
}

.circle-btn-green {
  border-radius: 50%;
  border: 1px solid #5fafa3;
}

table {
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

/* input[type="time"]::-webkit-datetime-edit-ampm-field {
  display: none;
} */
</style>
