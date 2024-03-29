<template>
  <div>
    <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
      {{ response }}
    </v-snackbar>
    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-dialog v-model="viewDialog" width="1400">
      <v-card>
        <v-card-title dense class="popup_background">
          Visitor Information - {{ item && item.full_name }}
          <v-spacer></v-spacer>
          <v-icon @click="viewDialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <Visitorinfo :key="item && item.id" :item="item"></Visitorinfo>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card elevation="1" class="mt-2" style="min-height: 500px">
      <v-toolbar class="mb-2 popup_background" dense flat v-if="!isDashboard">
        <v-toolbar-title>
          <span style="color: black" class="page-title-display">
            {{ title }}</span
          ></v-toolbar-title
        >
        <span>
          <v-btn
            title="Reload"
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            @click="getDataFromApi()"
            text
          >
            <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
          </v-btn>
        </span>
        <span>
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            title="Filter"
          >
            <v-icon @click="toggleFilter" class="mx-1 ml-2"
              >mdi mdi-filter</v-icon
            >
          </v-btn>
        </span>
        <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->

        <v-spacer></v-spacer>
        <!-- <CustomFilter
          style="margin-bottom: 5px"
          @filter-attr="filterAttr"
          :defaultFilterType="1"
          :height="'40px'"
          :default_date_from="from_date"
          :default_date_to="to_date"
        /> -->
      </v-toolbar>
      <v-data-table
        dense
        :headers="headers_table"
        :items="data"
        model-value="data.id"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-0 alternate-rows"
        :server-items-length="totalRowsCount"
        :hide-default-footer="isDashboard"
      >
        <template v-slot:header="{ props: { headers } }">
          <tr v-if="isFilter && !isDashboard">
            <td v-for="header in headers" :key="header.text">
              <v-container>
                <v-text-field
                  clearable
                  :hide-details="true"
                  v-if="header.filterable && !header.filterSpecial"
                  v-model="filters[header.value]"
                  id="header.value"
                  @input="applyFilters(header.value, $event)"
                  outlined
                  dense
                  autocomplete="off"
                ></v-text-field>
                <v-select
                  clearable
                  :hide-details="true"
                  @change="applyFilters('status', $event)"
                  item-value="id"
                  item-text="name"
                  v-model="filters[header.value]"
                  outlined
                  dense
                  v-else-if="
                    header.filterable &&
                    header.filterSpecial &&
                    header.value == 'status_id'
                  "
                  :items="[{ id: '', name: 'All' }, ...visitor_status_list]"
                ></v-select>
                <v-select
                  clearable
                  :hide-details="true"
                  @change="applyFilters('status', $event)"
                  item-value="id"
                  item-text="name"
                  v-model="filters[header.value]"
                  outlined
                  dense
                  v-else-if="
                    header.filterable &&
                    header.filterSpecial &&
                    header.value == 'purpose_id'
                  "
                  :items="[{ id: '', name: 'All Purposes' }, ...purposeList]"
                ></v-select>
                <v-select
                  v-if="header.filterSpecial && header.value == 'branch_id'"
                  :hide-details="true"
                  clearable
                  @change="applyFilters('status', $event)"
                  item-value="id"
                  item-text="branch_name"
                  v-model="filters[header.value]"
                  outlined
                  dense
                  :items="[
                    { branch_name: `All Branches`, id: `` },
                    ...branchesList,
                  ]"
                ></v-select>
                <v-autocomplete
                  clearable
                  :hide-details="true"
                  @change="applyFilters('status', $event)"
                  item-value="host_id"
                  item-text="full_name"
                  v-model="filters[header.value]"
                  outlined
                  dense
                  v-else-if="
                    header.filterable &&
                    header.filterSpecial &&
                    header.value == 'host_company_id'
                  "
                  :items="[
                    { host_id: '', full_name: 'All Hosts' },
                    ...hostList,
                  ]"
                ></v-autocomplete>

                <div
                  v-else-if="
                    header.filterable &&
                    header.filterSpecial &&
                    header.value == 'visit_from'
                  "
                  style="margin-top: -17px"
                >
                  <Calender
                    @filter-attr="filterAttr"
                    :defaultFilterType="1"
                    :height="'40px'"
                    :default_date_from="from_date"
                    :default_date_to="to_date"
                  />
                </div>
              </v-container>
            </td>
          </tr>
        </template>
        <template v-slot:item.sno="{ item, index }">
          {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + data.indexOf(item))
              : "-"
          }}
        </template>

        <template v-slot:item.phone_number_or_email="{ item }">
          <v-row no-gutters>
            <v-col
              style="
                padding: 5px;
                padding-left: 0px;
                width: 50px;
                max-width: 50px;
              "
            >
              <v-img
                style="
                  border: 1px solid #ddd;
                  border-radius: 50%;
                  height: auto;
                  width: 50px;
                  max-width: 50px;
                  height: 50px;
                "
                :src="item.logo ? item.logo : '/no-profile-image.jpg'"
              >
              </v-img>
            </v-col>
            <v-col style="padding: 10px">
              <strong>
                {{ item ? item.first_name : "---" }}
                {{ item ? item.last_name : "---" }}</strong
              >
              <div class="secondary-value">
                {{ item ? item.phone_number : "---" }}
              </div>
              <div class="secondary-value">
                {{ item ? item.email : "---" }}
              </div>
            </v-col>
          </v-row>
        </template>
        <template v-slot:item.first_name="{ item }">
          {{ item.full_name }}
        </template>
        <template v-slot:item.branch_id="{ item }">
          {{ (item.branch && item.branch.branch_name) || "---" }}

          <div class="secondary-value">
            {{ (item.host && item.host.floor_number) || "---" }} -
            {{ (item.host && item.host.flat_number) || "---" }} -
          </div>
        </template>
        <template v-slot:item.purpose_id="{ item }">
          {{ item.purpose.name }}
        </template>
        <template v-slot:item.visit_from="{ item }">
          {{ $dateFormat.format1(item.from_date_display) }}
          <div v-if="item.to_date_display != item.from_date_display">
            to {{ $dateFormat.format1(item.to_date_display) }}
          </div>
          <div class="secondary-value">
            {{ $dateFormat.time2Hm(item.time_in) }} -
            {{ $dateFormat.time2Hm(item.time_out) }}
          </div>
        </template>

        <!-- <template v-slot:item.phone_number_or_email="{ item }">
          {{ item.phone_number }}
          <br />
          <span class="secondary-value"> {{ item.email }}</span>
        </template> -->
        <template v-slot:item.visitor_company_name="{ item }"
          >{{ item.visitor_company_name }}
        </template>
        <template v-slot:item.id="{ item }">
          <span v-if="item.id_type == 1">Emirates ID</span>
          <span v-else-if="item.id_type == 2">National ID</span> <br />

          <span class="secondary-value"> {{ item.id_number }}</span>
        </template>

        <template v-slot:item.host_company_id="{ item }">
          {{ item.host?.employee.first_name || "---" }}
          {{ item.host?.employee.last_name }}
        </template>
        <template v-slot:item.rejected_reason="{ item }">
          <div style="color: red">
            {{ item.rejected_reason }}
          </div>
        </template>
        <template v-slot:item.status_id="{ item }">
          <span :style="'color:' + getRelatedColor(item)"
            >{{ item.status }}
            <div style="color: red">
              {{ verifyOverstay(item) }}
            </div>
            <div class="secondary-value" v-if="item.status_id >= 4">
              {{ item.system_user_id }}
            </div>
          </span>
        </template>
        <template v-slot:item.options="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list width="150" dense>
              <v-list-item @click="viewInfo(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="green" small> mdi-eye </v-icon>
                  View
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-if="can('host_edit')"
                @click="uploadVisitorInfo(item)"
              >
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="purple" small> mdi-cellphone-text </v-icon>
                  Upload Visitor to Device
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-if="item.system_user_id > 0"
                @click="viewUploadedVisitorInfo(item)"
              >
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="purple" small> mdi-eye </v-icon>
                  View Uploded info
                </v-list-item-title>
              </v-list-item>
              <!-- <v-list-item @click="updateStatus(item.id, 3)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="red" small> mdi-cancel</v-icon>
                  Reject
                </v-list-item-title>
              </v-list-item> -->
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>

    <div class="text-center">
      <v-dialog v-model="uploadUserToDeviceDialog" max-width="500px">
        <v-card>
          <v-card-title class="headline">Upload Visitor</v-card-title>
          <v-card-text class="mt-2">
            <v-form ref="form" v-model="valid">
              <v-text-field
                v-model="payload.system_user_id"
                label="Visitor ID"
                required
                outlined
                dense
                type="number"
                :rules="required"
              ></v-text-field>
              <v-text-field
                v-model="payload.card_rfid_number"
                label="RFID Card Number"
                required
                outlined
                dense
                type="number"
              ></v-text-field>
              <v-text-field
                v-model="payload.card_rfid_password"
                label="Password"
                required
                outlined
                dense
                type="number"
              ></v-text-field>
              <v-select
                v-model="payload.zone_id"
                :items="zoneList"
                label="Zone Devices"
                item-text="name"
                item-value="id"
                outlined
                dense
                required
                :rules="required"
              ></v-select>

              <!-- <v-row>
                <v-col>
                  <v-menu
                    ref="fromTimePicker"
                    v-model="fromTimePicker"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="payload.fromTime"
                        label="From Time"
                        outlined
                        dense
                        readonly
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-time-picker
                      v-if="fromTimePicker"
                      v-model="payload.fromTime"
                    ></v-time-picker>
                  </v-menu>
                </v-col>
                <v-col>
                  <v-menu
                    ref="toTimePicker"
                    v-model="toTimePicker"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="payload.toTime"
                        label="To Time"
                        outlined
                        dense
                        readonly
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-time-picker
                      v-if="toTimePicker"
                      v-model="payload.toTime"
                    ></v-time-picker>
                  </v-menu>
                </v-col>
              </v-row> -->
            </v-form>
          </v-card-text>
          <div style="font-size: 14px" class="pa-5">
            {{ response }}
          </div>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn dark color="grey" @click="cancel">Cancel</v-btn>
            <v-btn dark color="purple" @click="save" :disabled="!valid"
              >Save</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog v-model="dialog" width="500">
        <v-card>
          <v-toolbar flat dense>
            <v-spacer></v-spacer>
            <v-icon @click="dialog = false">mdi-close</v-icon>
          </v-toolbar>

          <v-card-text>
            <p class="text-center">
              <v-img
                :src="response_image"
                alt="Avatar"
                height="50px"
                width="50px"
                style="display: inline-block"
              ></v-img>
              <!-- <v-icon v-if="status_id == 1" color="green">mdi-check</v-icon>
              <v-icon v-else-if="status_id == 2" color="red">mdi-cancel</v-icon> -->
            </p>
            <p class="text-center">
              {{ message }}
            </p>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-dialog v-model="uploadedUserInfoDialog" max-width="500px">
        <v-card :loading="loadingDeviceData">
          <v-card-actions>
            <span>Reading Visitor info from Devices</span>
            <v-spacer></v-spacer>

            <v-icon outlined @click="uploadedUserInfoDialog = false"
              >mdi-close-circle</v-icon
            >
          </v-card-actions>

          <v-card-text class="mt-2">
            <v-card
              v-for="(visitor, index) in visitorUploadedDevicesInfo"
              :key="'vs' + index"
            >
              <v-card-title style="font-size: 13px"
                >{{ ++index }}: Device: {{ visitor.deviceName }}
              </v-card-title>
              <v-card-text class="mt-2">
                <v-row
                  class="100%"
                  style="margin: auto; line-height: 36px"
                  v-if="visitor.SDKresponseData.data"
                >
                  <v-col cols="4" style="padding: 0px">
                    <v-img
                      style="
                        border-radius: 10%;
                        width: 100px;
                        max-width: 95%;

                        height: auto;
                        border: 1px solid #ddd;
                      "
                      :src="
                        visitor.SDKresponseData.data.faceImage
                          ? 'data:image/jpeg;base64, ' +
                            visitor.SDKresponseData.data.faceImage
                          : '/no-profile-image.jpg'
                      "
                    >
                    </v-img>
                  </v-col>

                  <v-col cols="8" style="padding: 0px">
                    <v-simple-table>
                      <tr>
                        <td>Name</td>
                        <td>: {{ visitor.SDKresponseData.data.name }}</td>
                      </tr>

                      <tr>
                        <td>System User Id</td>
                        <td>: {{ visitor.SDKresponseData.data.userCode }}</td>
                      </tr>

                      <tr>
                        <td>Expiry Date</td>
                        <td>: {{ visitor.SDKresponseData.data.expiry }}</td>
                      </tr>
                      <tr>
                        <td>RFID Card Number</td>
                        <td>: {{ visitor.SDKresponseData.data.cardData }}</td>
                      </tr>
                      <tr>
                        <td>Password</td>
                        <td>
                          :
                          {{
                            visitor.SDKresponseData.data.password.replaceAll(
                              "F",
                              ""
                            )
                          }}
                        </td>
                      </tr>

                      <tr>
                        <td>Timezone Group Id</td>
                        <td>: {{ visitor.SDKresponseData.data.timeGroup }}</td>
                      </tr>
                    </v-simple-table>
                  </v-col>
                </v-row>

                <div v-else>{{ visitor.SDKresponseData.message }}</div>

                <v-row>
                  <v-col cols="12">
                    <v-btn
                      v-if="can('visitor_delete')"
                      class="align-right"
                      style="float: right; color: #fff"
                      dense
                      small
                      color="red"
                      @click="deleteFromDevice(visitor)"
                      >Delete</v-btn
                    >
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<script>
import Visitorinfo from "../../components/Visitor/VisitorInfo.vue";
export default {
  props: [
    "title",
    "filterValue",
    "isDashboard",
    "statsFilterValue",
    "defaultDates",
  ],
  components: { Visitorinfo },
  data: () => ({
    loadingDeviceData: false,
    overlay: false,
    snackbar: false,
    response: "",
    required: [(v) => !!v || "Required"],
    visitor_status_list: [],
    uploadUserToDeviceDialog: false,
    uploadedUserInfoDialog: false,
    valid: false,
    fromTimePicker: false,
    toTimePicker: false,

    payload: {
      visitor_id: null,
      zone_id: 1,
      fromTime: null,
      toTime: null,
    },
    zoneList: [
      { id: 1, name: "Zone 1" },
      { id: 2, name: "Zone 2" },
      // Add more zones as needed
    ],
    hostList: [],
    item: { purpose: {} },
    viewDialog: false,
    formAction: "Create",
    previewImage: null,

    upload: {
      name: "",
    },
    isFilter: false,
    filters: [],
    loading: false,
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    totalRowsCount: 0,
    options: { perPage: 10 },
    status_id: 0,
    response_image: "/sucess.png",
    dialog: false,
    message: "",
    branchesList: [],
    changeRequestDialog: false,
    Model: "Visitor Request",
    endpoint: "visitor",
    data: [],
    from_date: "",
    to_date: "",
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: false,
        value: "sno",
        filterable: false,
      },
      {
        width: "250px",
        text: "Picture",
        align: "left",
        sortable: true,
        value: "phone_number_or_email",
        filterable: true,
      },
      // {
      //   text: "Name",
      //   align: "left",
      //   sortable: true,
      //   value: "first_name",
      //   filterable: true,
      //   filterSpecial: false,
      // },
      {
        text: "Purpose",
        align: "left",
        sortable: true,
        value: "purpose_id",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Date",
        align: "left",
        sortable: true,
        value: "visit_from",
        filterable: true,
        filterSpecial: true,
      },
      // {
      //   text: "Time",
      //   align: "left",
      //   sortable: true,
      //   value: "time_in",
      //   filterable: true,
      //   filterSpecial: false,
      // },
      // {
      //   text: "Contact Number",
      //   align: "left",
      //   sortable: true,
      //   value: "phone_number_or_email",
      //   filterable: true,
      //   filterSpecial: false,
      // },
      {
        text: "From Company",
        align: "left",
        sortable: true,
        value: "visitor_company_name",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Host",
        align: "left",
        sortable: true,
        value: "host_company_id",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Status",
        align: "left",
        sortable: true,
        value: "status_id",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Options",
        align: "left",
        sortable: false,
        value: "options",
        filterable: false,
      },
    ],
    branchesList: [],
    branch_id: null,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    purposeList: [],
    selectedVisitor: null,
    visitorUploadedDevicesInfo: [],
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },

    // filterValue: {
    //   handler(val) {
    //     this.getDataFromApi(val);
    //   },
    //   deep: true,
    // },
  },
  created() {
    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
    this.to_date = today.toISOString().slice(0, 10);

    if (this.defaultDates && this.defaultDates.length) {
      this.from_date = this.defaultDates[0];
      this.to_date = this.defaultDates[1];
    }

    this.getDataFromApi();

    if (this.$auth.user.branch_id == null || this.$auth.user.branch_id == 0) {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          value: "branch_id",
          filterable: true,
          filterName: "branch_id",
          filterSpecial: true,
        },
      ];
      this.headers_table.splice(2, 0, ...branch_header);
    }
    if (this.statsFilterValue == "Rejected") {
      this.headers_table = this.headers_table.filter(
        (item) => item.value != "visitor_company_name"
      );

      let rejected_header = [
        {
          text: "Reason",
          align: "left",
          sortable: true,
          value: "rejected_reason",
          filterable: true,
          filterSpecial: true,
        },
      ];
      this.headers_table.splice(7, 0, ...rejected_header);
    }
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    deleteFromDevice(item) {
      if (confirm("Are you sure want to Delete From This Device?")) {
        let options = {
          params: {
            company_id: this.$auth.user.company_id,
            visitor_id: item.visitor_id,
            system_user_id: item.system_user_id,

            device_id: item.device_id,
          },
        };
        this.$axios
          .post(`delete-visitor-from-devices`, options.params)
          .then(({ data }) => {
            this.response = data.message;
            this.snackbar = true;

            if (data.status) {
              this.uploadedUserInfoDialog = false;
            }
          });
      }
    },
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,

          // branch_id: this.$auth.user.branch_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
      });
    },
    verifyOverstay(item) {
      if (item.attendances && item.attendances[0]) {
        if (item.status_id >= 6) {
          let inTime = item.attendances[0].in;
          let outTime = item.attendances[0].out;
          let overTimeInSeconds = 0;
          if (outTime == "" || outTime == null) {
            outTime = this.getCurrentTime();
          }

          if (inTime && outTime) {
            overTimeInSeconds =
              this.getTimeInSeconds(outTime) - this.getTimeInSeconds(inTime);

            if (overTimeInSeconds > 0) {
              return (
                "Over Stay : " + this.formatSecondsToTime(overTimeInSeconds)
              );
            }
          }
        }
      }
      return "";
    },
    formatSecondsToTime(totalSeconds) {
      let hours = Math.floor(totalSeconds / 3600);
      let minutes = Math.floor((totalSeconds % 3600) / 60);
      let seconds = totalSeconds % 60;

      return hours + "h:" + minutes + "m";
    },
    getTimeInSeconds(time) {
      let timeArray = time.split(":");
      let hours = parseInt(timeArray[0], 10);
      let minutes = parseInt(timeArray[1], 10);
      let seconds = parseInt(timeArray[2], 10);

      // Convert hours, minutes, and seconds to total seconds
      let totalSeconds = hours * 3600 + minutes * 60 + seconds;

      return totalSeconds;
    },
    getCurrentTime() {
      let currentTime = new Date();

      let hours = currentTime.getHours();
      let minutes = currentTime.getMinutes();
      let seconds = currentTime.getSeconds();

      // Format the time as a string
      let formattedTime = hours + ":" + minutes + ":" + seconds;

      return formattedTime;
    },
    async viewUploadedVisitorInfo(item) {
      this.uploadedUserInfoDialog = true;

      this.visitorUploadedDevicesInfo = [];
      this.loadingDeviceData = true;
      let counter = 1;
      await item.zone.devices.forEach((element) => {
        let options = {
          params: {
            company_id: this.$auth.user.company_id,
            visitor_id: item.id,
            system_user_id: item.system_user_id,
            zone_id: item.zone_id,
            device_id: element.device_id,
          },
        };
        this.loadingDeviceData = true;
        this.$axios
          .get(`get-device-person-details`, options)
          .then(({ data }) => {
            if (item.zone.devices.length == counter) {
              this.loadingDeviceData = false;
            }
            counter++;
            if (!data.deviceName) {
              this.response = data.message;
              this.snackbar = true;

              return;
            } else {
              data.system_user_id = item.system_user_id;
              data.device_id = element.device_id;
              data.visitor_id = item.id;

              this.visitorUploadedDevicesInfo.push(data);

              return;
            }
          });
      });
    },
    uploadVisitorInfo(item) {
      this.response = "";
      this.selectedVisitor = item;
      //this.uploadVisitorId = item.id;
      this.uploadUserToDeviceDialog = true;
      this.payload.system_user_id = item.system_user_id;
      this.payload.zone_id = item.zone_id;
      this.payload.card_rfid_number = item.card_rfid_number;
      this.payload.card_rfid_password = item.card_rfid_password;
    },
    cancel() {
      this.uploadUserToDeviceDialog = false;
    },
    save() {
      if (this.$refs.form.validate()) {
        //this.uploadUserToDeviceDialog = false;

        let options = {
          params: {
            company_id: this.$auth.user.company_id,
            visitor_id: this.selectedVisitor.id,
            system_user_id: this.payload.system_user_id,
            zone_id: this.payload.zone_id,
            card_rfid_number: this.payload.card_rfid_number,
            card_rfid_password: this.payload.card_rfid_password,
          },
        };

        this.response =
          "Visitor Profile picture is uploading to Device. Please wait for 5 to 10 seconds";
        this.snackbar = true;
        this.valid = false;

        this.overlay = true;
        this.$axios
          .post(`visitor-update-zone`, options.params)
          .then(({ data }) => {
            this.loading = false;
            this.valid = false;
            if (!data.status) {
              this.response = data.message;
              this.snackbar = true;
              this.overlay = false;
              this.valid = true;
              return;
            } else {
              setTimeout(() => {
                this.overlay = false;
                this.valid = true;
                this.uploadUserToDeviceDialog = false;

                this.response = "Visitor Zone details are updated successfully";
                this.snackbar = true;

                this.getDataFromApi();
              }, 2000);

              return;
            }
          });

        // setTimeout(() => {

        // }, 3000);
      }
    },
    viewInfo(item) {
      this.viewDialog = true;
      this.item = item;
    },
    filterAttr(data) {
      if (data != null) {
        this.from_date = data.from;
        this.to_date = data.to;
      } else {
        this.from_date = null;
        this.to_date = null;
      }

      this.applyFilters();
    },
    applyFilters() {
      this.getDataFromApi();
      this.$emit("changeBranch", this.filters["branch_id"]);
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
      if (this.isFilter) {
        this.getPurposeList();
        this.getHostsList();
        this.getbranchesList();
        this.getVisitorStatusList();
      }
    },
    getPurposeList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`purpose_list`, options).then(({ data }) => {
        this.purposeList = data;
      });
    },
    getHostsList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`host_list`, options).then(({ data }) => {
        this.hostList = [];

        // Loop through the data and extract employee_id and full_name
        for (let i = 0; i < data.length; i++) {
          let item = data[i];
          let employee = item.employee;
          if (employee) {
            // Add the extracted information to the array
            this.hostList.push({
              host_id: item.id,
              full_name: employee.full_name,
            });
          }
        }
      });
    },
    getVisitorStatusList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`visitor_status_list`, options).then(({ data }) => {
        this.visitor_status_list = data;
      });
    },
    // filterAttr(data) {
    //   this.from_date = data.from;
    //   this.to_date = data.to;
    //   this.getDataFromApi();
    // },
    updateStatus(id, status_id) {
      this.status_id = status_id;
      this.$axios
        .post(`visitor-status-update/${id}`, {
          status_id: status_id,
        })
        .then(({ data }) => {
          if (!data.status) {
            this.message = data.message;
            this.response_image = "/fail.png";
            setTimeout(() => (this.dialog = false), 3000);
            return;
          }
          this.message = "Your clocking has been recorded successfully";
          if (status_id == 1) {
            this.response_image = "/success.png";
          } else {
            this.response_image = "/fail.png";
          }
          this.dialog = true;
          this.message = data.message;
          this.getDataFromApi();
        });
    },
    getRelatedColor(item) {
      let colors = {
        1: "purple",
        3: "red",
        2: "green",
        UNKNOWN: "purple",
      };
      return colors[item.status_id || "UNKNOWN"];
    },
    getDataFromApi(filterValue = null) {
      this.loading = true;

      // if (this.filterValue == "Total Visitor") {
      //   this.filters["status_id"] = null;
      // } else if (this.filterValue == "Approved") {
      //   this.filters["status_id"] = 2;
      // } else if (this.filterValue == "Rejected") {
      //   this.filters["status_id"] = 3;
      // } else if (this.filterValue == "Over Stayed") {
      //   this.filters["status_id"] = null;
      // }
      // if (this.filterValue != "") {
      //   this.filters["status_id"] = null;
      // }

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          pagination: true,
          company_id: this.$auth.user.company_id,

          from_date: this.from_date,
          to_date: this.to_date,
          ...this.filters,
          statsFilterValue: this.statsFilterValue,
        },
      };
      this.currentPage = page;
      this.perPage = itemsPerPage;
      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },
  },
};
</script>
