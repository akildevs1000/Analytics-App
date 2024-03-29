<template>
  <div v-if="can(`timezone_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="dialogManualInput" width="300px">
      <v-card>
        <v-card-title dense class="popup_background">
          <span>Select Hour Range</span>
          <v-spacer></v-spacer>
          <v-icon @click="dialogManualInput = false" outlined>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-container style="min-height: 100px">
            <v-row>
              <v-col md="6">
                <v-select
                  height="20px"
                  outlined
                  v-model="dialog_time_start"
                  dense
                  :items="seasons"
                ></v-select>
              </v-col>
              <v-col md="6">
                <v-select
                  outlined
                  v-model="dialog_time_end"
                  dense
                  :items="seasons"
                ></v-select>
              </v-col>

              <v-spacer></v-spacer>
              <v-btn dark color="violet" fill @click="selectTimeRange()"
                >Update</v-btn
              >
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog persistent v-model="dialog" width="80%" :key="editedIndex">
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
            <v-col v-if="viewmode" cols="6">
              <div>
                <strong class="">Branch</strong>:
                {{
                  editedItem &&
                  editedItem.branch &&
                  editedItem.branch.branch_name
                }}
              </div>
            </v-col>

            <v-col v-if="!viewmode && isCompany" cols="6">
              <v-select
                class="pt-2"
                v-model="editedItem.branch_id"
                :items="branchesList"
                dense
                placeholder="Select Branch"
                outlined
                item-value="id"
                item-text="branch_name"
              >
              </v-select>
              <span
                class="error--text"
                v-if="
                  !viewmode && errors && errors.branch_id && errors.branch_id[0]
                "
              >
                {{ errors.branch_id[0] }}
              </span>
            </v-col>

            <v-col>
              <div v-if="viewmode">
                <strong class="">Zone</strong>:
                {{ editedItem && editedItem.timezone_name }}
              </div>
              <v-text-field
                dense
                small
                placeholder="Timezone Name"
                width="80px"
                style="padding-top: 8px"
                v-model="editedItem.timezone_name"
                outlined
                v-else
              ></v-text-field>
              <span
                class="error--text"
                v-if="
                  !viewmode &&
                  errors &&
                  errors.timezone_name &&
                  errors.timezone_name[0]
                "
              >
                {{ errors.timezone_name[0] }}
              </span>
            </v-col>
            <v-col>
              <div v-if="viewmode">
                <strong class="">Description</strong>:{{
                  editedItem.description
                }}
              </div>
              <v-text-field
                v-else
                dense
                small
                outlined
                placeholder="Description"
                width="150px"
                style="padding-top: 8px"
                v-model="editedItem.description"
              ></v-text-field>
              <span
                class="error--text"
                v-if="
                  !viewmode &&
                  errors &&
                  errors.description &&
                  errors.description[0]
                "
              >
                {{ errors.description[0] }}
              </span>
            </v-col>
            <v-col
              v-if="!viewmode"
              md="4"
              style="float: right; text-align: right"
            >
              <v-btn
                small
                dense
                dark
                color="violet"
                fill
                @click="clearSelection()"
              >
                Cancel
              </v-btn>
              <v-btn small dense dark color="violet" fill @click="submit"
                >Submit</v-btn
              >
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-text>
          <table style="width: 100%">
            <thead>
              <tr>
                <th></th>
                <th
                  v-for="(slot, slotIndex) in timeSlots"
                  :key="slot"
                  class="settings-time"
                >
                  <div :title="getSlotTitle(slot, timeSlots[slotIndex + 1])">
                    {{ slot }}
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(day, index) in days" :key="index">
                <td>{{ day.name }}</td>
                <td
                  v-for="(slot, slotIndex) in timeSlots"
                  :key="slot"
                  :title="getSlotTitle(slot, timeSlots[slotIndex + 1])"
                  @click="!viewmode && toggleCellBackground(index, slotIndex)"
                  :class="
                    selectedCells.has(index + '-' + slotIndex)
                      ? 'tdcell selected'
                      : 'tdcell un-selected'
                  "
                  :id="`cell_${index}_${slotIndex}`"
                ></td>
                <td>
                  <img
                    v-if="!viewmode"
                    @click="manualINputSettings(index)"
                    title="Manual Input"
                    src="/../../icons/always_open.png"
                    style="width: 33px; pointer: cursor"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </v-card-text>
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
                <td colspan="3" class="text-center">No Data available</td>
              </tr>
            </tbody>
          </table>
          <!-- <br />
          <v-btn small color="grey white--text" @click="syncDeviceDialog = false">
            Close</v-btn> -->
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card class="mt-2">
      <v-toolbar class="rounded-md" dense flat>
        <v-toolbar-title><span> Timezones List</span></v-toolbar-title>
        <span>
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
        </span>
        <div v-if="isCompany" style="width: 250px">
          <v-select
            @change="getDataFromApi()"
            class="pt-10 px-2"
            v-model="branch_id"
            :items="[{ id: ``, branch_name: `Select All` }, ...branchesList]"
            dense
            placeholder="Select Branch"
            outlined
            item-value="id"
            item-text="branch_name"
          >
          </v-select>
        </div>

        <v-spacer></v-spacer>

        <span>
          <v-btn
            x-small
            :ripple="false"
            text
            title="Sync To Devices"
            @click="openDeviceDialog"
          >
            <v-icon dark fill color="violet"> mdi-sync-circle</v-icon>
          </v-btn>
        </span>
        <span>
          <v-btn
            v-if="can(`timezone_create`)"
            x-small
            :ripple="false"
            text
            title="Add Timezone"
            @click="addItem"
          >
            <v-icon dark white>mdi-plus-circle</v-icon>
          </v-btn>
        </span>
      </v-toolbar>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <v-data-table
        dense
        :headers="headers"
        :items="data"
        model-value="data.id"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-1"
      >
        <template v-slot:item.sno="{ item, index }">
          {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + data.indexOf(item))
              : ""
          }}
        </template>

        <template v-slot:item.member="{ item }">
          {{ item.employee_device && item.employee_device.employee_ids.length }}
        </template>

        <template v-slot:item.menu="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list width="120" dense>
              <v-list-item @click="viewItem(item)" v-if="can(`timezone_view`)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-eye </v-icon>
                  View
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-if="can(`timezone_edit`)" @click="editItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-pencil </v-icon>
                  Edit
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-if="can(`timezone_delete`)"
                @click="deleteItem(item)"
              >
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
    viewmode: false,
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,

    snackbar: false,
    response: "",
    dialog_time_start: "",

    dialog_time_end: "",
    dialogManualInput: false,
    seasons: [
      0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
      21, 22, 23, 24,
    ],
    span_time_minutes: 30,

    timeSlots: {}, // Generate time slots for 24 hours with 30-minute intervals
    selectedCells: new Set(),
    array: [],

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
    errors: {},

    days,
    editedItem: {
      timezone_id: "",
      timezone_name: "Timezone Name",
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
      timezone_name: "Timezone Name",
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
    headers: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "sno",
        value: "sno",
      },

      {
        text: "Zone",
        align: "left",
        sortable: true,
        key: "timezone_name",
        value: "timezone_name",
      },

      {
        text: "Description",
        align: "left",
        sortable: true,
        key: "description",
        value: "description",
      },
      {
        text: "Member",
        align: "left",
        sortable: true,
        key: "member",
        value: "member",
      },
      {
        text: "Created",
        align: "left",
        sortable: false,
        key: "updated_at",
        value: "updated_at",
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

    branchesList: [],
    branch_id: "",
    isCompany: true,
  }),

  computed: {},

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = {};
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
  async created() {
    this.timeSlots = this.generateTimeSlots(24);
    this.loading = true;
    this.loading_dialog = true;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    let branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id", //sorting
        value: "branch.branch_name", //edit purpose
        width: "300px",
        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(1, 0, ...branch_header);

    try {
      const { data } = await this.$axios.get(`branches_list`, {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      });
      this.branchesList = data;
    } catch (error) {
      // Handle the error
      console.error("Error fetching branch list", error);
    }
  },

  methods: {
    getSlotTitle(slot, slot2) {
      slot2 = slot2 != undefined ? slot2 : "24:00";
      return slot + " to " + slot2;
    },
    updateIndex(page) {
      this.currentPage = page;
      this.cumulativeIndex = (page - 1) * this.perPage;
    },
    manualINputSettings(day_index) {
      this.day_index = day_index;
      this.dialogManualInput = true;
    },
    generateTimeSlots(hours) {
      let interval = 30; // this.span_time_minutes;
      this.span_time_minutes = 30;
      const timeSlots = [];
      for (let hour = 0; hour < hours; hour++) {
        for (let minute = 0; minute < 60; minute += interval) {
          const formattedHour = `${hour.toString().padStart(2, "0")}:${minute
            .toString()
            .padStart(2, "0")}`;
          timeSlots.push(formattedHour);
        }
      }
      return timeSlots;
    },
    generateTimeSlotsRange(start, end) {
      let interval = 30; // this.span_time_minutes;
      this.span_time_minutes = 30;
      const timeSlots = [];
      for (let hour = start; hour < end; hour++) {
        for (let minute = 0; minute < 60; minute += interval) {
          const formattedHour = `${hour.toString().padStart(2, "0")}:${minute
            .toString()
            .padStart(2, "0")}`;
          timeSlots.push(formattedHour);
        }
      }
      return timeSlots;
    },
    selectTimeRange() {
      let timeArray = this.generateTimeSlotsRange(
        this.dialog_time_start,
        this.dialog_time_end
      );
      timeArray.forEach((element) => {
        let columnIndex = this.timeSlots.findIndex((item) => item == element);

        this.toggleCellBackground(this.day_index, columnIndex, true);
      });
      this.dialogManualInput = false;
    },
    toggleCellBackground(rowIndex, columnIndex, isPopup = false) {
      const refName = `cell_${rowIndex}_${columnIndex}`;
      const printableContent = document.getElementById(refName);

      const key = `${rowIndex}-${columnIndex}`;

      if (this.selectedCells.has(key)) {
        if (!isPopup) {
          this.selectedCells.delete(key);
          if (printableContent) {
            printableContent.classList.add("un-selected");
            printableContent.classList.remove("selected");
          }

          // printableContent.style.backgroundColor = "#DDD";
        }
      } else {
        this.selectedCells.add(key);

        if (printableContent) {
          printableContent.classList.add("selected");
          printableContent.classList.remove("un-selected");
        }
      }
    },
    isSelected(rowIndex, columnIndex) {
      return this.selectedCells.has(`${rowIndex}-${columnIndex}`);
    },
    clearSelection() {
      const elementsArray = document.getElementsByClassName("tdcell");

      elementsArray.forEach((element, index, array) => {
        element.classList.remove("selected");
        element.classList.add("un-selected");
      });
      this.selectedCells = new Set();
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
    // processInput(index, interval, type, input) {
    //   this.editedItem.interval[index] = { "begin": interval,type, input };
    // },
    onPageChange() {
      this.getDataFromApi();
    },
    addItem() {
      this.viewmode = false;
      this.clearSelection();
      this.dialog = true;
      this.readOnly = false;
      this.editedIndex = -1;
      this.editedItem = this.defaultItem;

      if (!this.isCompany) {
        this.editedItem.branch_id = this.branch_id;
      }
    },
    viewItem(item) {
      this.viewmode = true;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.readOnly = false;
      this.editedIndex = this.data.indexOf(item);

      this.editedItem = Object.assign({}, item);
      this.clearSelection();

      let intervals_raw_data = JSON.parse(item.intervals_raw_data);

      intervals_raw_data.forEach((element) => {
        console.log(element);
        const myArray = element.split("-");
        this.toggleCellBackground(myArray[0], myArray[1]);
      });
    },
    editItem(item) {
      this.viewmode = false;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.readOnly = false;
      this.editedIndex = this.data.indexOf(item);

      this.editedItem = Object.assign({}, item);
      this.clearSelection();

      let intervals_raw_data = JSON.parse(item.intervals_raw_data);

      if (!intervals_raw_data) return;

      intervals_raw_data.forEach((element) => {
        console.log(element);
        const myArray = element.split("-");
        this.toggleCellBackground(myArray[0], myArray[1]);
      });
    },
    clearSelection() {
      const elementsArray = document.getElementsByClassName("tdcell");

      elementsArray.forEach((element, index, array) => {
        element.classList.remove("selected");
        element.classList.add("un-selected");
      });
      this.selectedCells = new Set();
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

      this.editedItem.company_id = this.$auth.user.company_id;

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
          branch_id: this.branch_id,
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

      console.log(sortedDays);
      this.editedItem["scheduled_days"] = sortedDays;

      this.editedItem.company_id = this.$auth.user.company_id;

      const myArray = Array.from(this.selectedCells);
      const jsonString = JSON.stringify(myArray);

      this.editedItem.intervals_raw_data = jsonString;

      this.editedItem.input_time_slots = this.timeSlots;

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

/* table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
} */

/* tr:nth-child(even) {
  background-color: #dddddd;
} */

/* input[type="time"]::-webkit-datetime-edit-ampm-field {
  display: none;
} */

table {
  border-collapse: collapse;
  width: 100%;
  overflow: auto;
}
th {
  font-size: 13px;
  font-weight: 300;
}
th:nth-child(even) {
  font-weight: bold;
}
th,
td {
  border: 1px solid #dddddd;
  text-align: center;
}

th {
  background-color: #f2f2f2;
}

.un-selected {
  background-color: #ddd;
  border: 1px solid #fff;
}
.selected {
  background-color: #60ad60;
  border: 1px solid #fff;
}
.selected-cell {
  background-color: green; /* Change this color to the desired highlight color */
}

/*#60ad60 */

.settings-time {
  font-size: 10px;
}
.v-input__control .v-input__slot,
.v-input__slot,
input {
  min-height: auto !important;
  display: flex !important;
  align-items: center !important;
}
</style>
