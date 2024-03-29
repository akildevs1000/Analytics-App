<template>
  <div v-if="employeeObject">
    <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
      {{ response }}
    </v-snackbar>
    <v-row class="pt-5">
      <v-col cols="3">
        <v-card elevation="2" style="min-height: 925px">
          <v-row>
            <v-col cols="6" class="text-left pa-5">
              <v-chip
                v-if="employeeObject.status"
                color="green"
                filter
                label
                outlined
                >Active</v-chip
              >
              <v-chip v-else color="red" filter label outlined
                >In-Active</v-chip
              >
            </v-col>
            <v-col cols="6" class="text-end pa-5">
              <v-chip
                v-if="employeeObject.status"
                color="green"
                filter
                label
                outlined
                ><v-icon style="margin-top: -2px">mdi-identifier</v-icon>
                {{ employeeObject.employee_id }}</v-chip
              >
            </v-col>
            <v-col cols="12" class="pb-0">
              <div class="mt-5" style="margin: 0 auto; width: 200px">
                <v-img
                  style="
                    width: 100%;
                    height: 200px;
                    border: 1px solid #5fafa3;
                    border-radius: 15px;
                    margin: 0 auto;
                  "
                  :src="
                    employeeObject.profile_picture || '/no-profile-image.jpg'
                  "
                ></v-img>
                <br />
                <div class="text-center">
                  <strong>{{ employeeObject.full_name }}</strong>
                </div>
                <div class="text-center text-center">
                  <strong
                    ><v-icon style="vertical-align: baseline" color="violet"
                      >mdi-account-tie</v-icon
                    >Employee ID: {{ employeeObject.employee_id }}</strong
                  >
                </div>
                <div class="text-center pt-2 mb-2">
                  <span v-html="formatJoiningDate"></span>
                  <span style="float: right; margin-right: -60px"
                    ><a
                      title="Download Profile PDF"
                      :href="
                        getDonwloadLink(
                          employeeObject.profile_picture_raw,
                          employeeObject.id
                        )
                      "
                    >
                      <img src="/icons/icon_pdf.png" class="iconsize" /></a
                  ></span>
                  <!-- <span style="float: right; margin-right: -60px"
                    ><a
                      title="Download Profile Picture"
                      :href="
                        getDonwloadLink(
                          employeeObject.profile_picture_raw,
                          employeeObject.employee_id
                        )
                      "
                      ><v-icon color="violet"
                        >mdi-arrow-down-bold-circle</v-icon
                      ></a
                    ></span
                  > -->
                </div>
              </div>
              <hr />
            </v-col>
            <v-col cols="12" class="pt-0">
              <table class="view-profile-table-lineheight">
                <!-- <tr>
                        <td>
                          <strong>Display Name </strong> :
                          {{ employeeObject.display_name }}
                        </td>
                      </tr> -->

                <tr>
                  <td style="text-align: left">
                    <v-icon color="violet">mdi-cellphone-settings</v-icon>
                  </td>
                  <td style="text-align: right">
                    {{ employeeObject.phone_number || "---" }}
                  </td>
                </tr>
                <tr>
                  <td style="text-align: left">
                    <v-icon color="violet">mdi-email-outline</v-icon>
                  </td>
                  <td style="text-align: right">
                    {{ employeeObject.local_email || "" }}
                  </td>
                </tr>

                <tr>
                  <td>
                    <v-icon color="violet">mdi-login-variant</v-icon>
                  </td>
                  <td style="text-align: right">
                    Last Login :
                    {{ last_login ? last_login : "---" }}
                  </td>
                </tr>
                <tr>
                  <td>
                    <v-switch
                      style="margin-top: 0px"
                      disabled
                      v-model="employeeObject.status"
                    ></v-switch>
                  </td>
                  <td style="text-align: right">Web Login</td>
                </tr>
                <tr>
                  <td>
                    <v-switch
                      style="margin-top: 0px"
                      disabled
                      v-model="employeeObject.mobile_application"
                    ></v-switch>
                  </td>
                  <td style="text-align: right">Mobile Login</td>
                </tr>
                <tr>
                  <td style="text-align: left"><strong>Dep</strong></td>
                  <td style="text-align: right">
                    {{ employeeObject.department.name }}
                  </td>
                </tr>
                <tr>
                  <td style="text-align: left">
                    <strong>Sub </strong>
                  </td>

                  <td style="text-align: right">
                    {{ employeeObject.sub_department.name }}
                  </td>
                </tr>
                <tr>
                  <td style="text-align: left">
                    <strong>Desg </strong>
                  </td>
                  <td style="text-align: right">
                    {{ employeeObject.designation.name }}
                  </td>
                </tr>
                <tr style="border-bottom: 0px">
                  <td style="text-align: left">
                    <strong>Role </strong>
                  </td>
                  <td style="text-align: right">
                    {{ employeeObject?.user?.role?.name || "---" }}
                  </td>
                </tr>
                <!-- <tr>
                        <td colspan="2"><v-divider></v-divider></td>
                      </tr> -->
              </table>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="9" class="pa-0">
        <v-card elevation="2" style="min-height: 925px">
          <v-tabs
            v-model="tabmain"
            background-color="transparent"
            color="violet"
            grow
            flat
          >
            <v-tab> Attendance </v-tab>
            <v-tab> Schedule </v-tab>
            <v-tab> leave Quota </v-tab>
            <v-tab> Payslips </v-tab>
            <v-tab> Profile</v-tab>
            <v-tab @click="loadMappedDevicesList()"> Mapped Devices</v-tab>

            <v-tabs-slider color="violet"></v-tabs-slider>
            <v-tabs-items v-model="tabmain">
              <v-tab-item>
                <v-row class="pt-5 pb-3">
                  <v-col md="2" class="align-left">
                    <v-icon
                      size="40"
                      @click="updateCalender(--calender_month_switcher)"
                      style="cursor: pointer"
                    >
                      mdi-less-than</v-icon
                    ></v-col
                  >
                  <v-col md="8" class="text-center" style="font-size: 30px"
                    ><v-icon size="40" fill>mdi-calendar-month</v-icon>
                    {{ month_year_display }}
                  </v-col>
                  <v-col md="2" class="align-right text-end">
                    <v-icon
                      size="40"
                      @click="updateCalender(++calender_month_switcher)"
                      style="cursor: pointer"
                    >
                      mdi-greater-than</v-icon
                    ></v-col
                  >
                </v-row>

                <v-divider></v-divider>
                <v-row>
                  <v-col md="3" class="text-center">
                    <v-row>
                      <v-col cols="4" class="text-end">
                        <v-icon size="50">mdi-clock-check-outline</v-icon>
                      </v-col>
                      <v-col class="text-left"
                        ><div class="bold">{{ avg_clock_in }}</div>
                        Avg Clock In
                      </v-col>
                    </v-row>
                  </v-col>

                  <v-col
                    md="3"
                    class="text-center"
                    style="border-left: 1px solid #ddd"
                  >
                    <v-row>
                      <v-col cols="4" class="text-end">
                        <v-icon size="50">mdi-clock-check-outline</v-icon>
                      </v-col>
                      <v-col class="text-left"
                        ><div class="bold">{{ avg_clock_out }}</div>
                        Avg Clock Out
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col
                    md="3"
                    class="text-center"
                    style="border-left: 1px solid #ddd"
                  >
                    <v-row>
                      <v-col cols="4" class="text-end">
                        <v-icon size="50">mdi-account-clock</v-icon>
                      </v-col>
                      <v-col class="text-left"
                        ><div class="bold">{{ avg_working_hours }}</div>
                        Avg Working Hr.
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col
                    md="3"
                    class="text-center"
                    style="border-left: 1px solid #ddd"
                  >
                    <v-row>
                      <v-col cols="4" class="text-end">
                        <v-icon size="50">mdi-calendar-month</v-icon>
                      </v-col>
                      <v-col class="text-left"
                        ><div class="bold">{{ presents }} / {{ leaves }}</div>
                        Presents / Leaves
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-divider></v-divider>
                <!-- <DashboardRealTimeLogTableview
                    v-if="employeeObject.system_user_id > 0"
                    :system_user_id="employeeObject.system_user_id"
                    :key="employeeObject.system_user_id"
                  /> -->
                <v-tabs
                  class="slidegroup1"
                  v-model="tab"
                  background-color="transparent"
                  right
                  dark
                  color="violet"
                  flat
                >
                  <v-tabs-slider
                    class="violet slidegroup1"
                    style="height: 3px"
                  ></v-tabs-slider>

                  <v-tab
                    @click="commonMethod()"
                    :key="1"
                    style="height: 30px"
                    href="#tab-1"
                    class="black--text slidegroup1"
                  >
                    Single
                  </v-tab>

                  <v-tab
                    :key="2"
                    @click="commonMethod()"
                    style="height: 30px"
                    href="#tab-2"
                    class="black--text slidegroup1"
                  >
                    Double
                  </v-tab>

                  <v-tab
                    :key="3"
                    @click="commonMethod"
                    style="height: 30px"
                    href="#tab-3"
                    class="black--text slidegroup1"
                  >
                    Multi
                  </v-tab>
                </v-tabs>

                <v-tabs-items v-model="tab">
                  <v-tab-item value="tab-1">
                    <AttendanceReport
                      :key="1"
                      title="General Reports"
                      shift_type_id="1"
                      :headers="generalHeaders"
                      :report_template="report_template"
                      :payload1="payload11"
                      process_file_endpoint=""
                      render_endpoint="render_general_report"
                      @genRecordCount="genRecordCount"
                      :display_emp_pic="false"
                      :system_user_id="system_user_id"
                    />
                  </v-tab-item>
                  <v-tab-item value="tab-2">
                    <AttendanceReport
                      title="Split Reports"
                      shift_type_id="5"
                      :headers="doubleHeaders"
                      :report_template="report_template"
                      :payload1="payload11"
                      process_file_endpoint="multi_in_out_"
                      render_endpoint="render_multi_inout_report"
                      :key="2"
                      ref="profile"
                      @dualRecordCount="dualRecordCount"
                      :display_emp_pic="false"
                      :system_user_id="system_user_id"
                    />
                  </v-tab-item>
                  <v-tab-item value="tab-3">
                    <AttendanceReport
                      :key="3"
                      title="Multi In/Out Reports"
                      shift_type_id="2"
                      :headers="multiHeaders"
                      :report_template="report_template"
                      :payload1="payload11"
                      process_file_endpoint="multi_in_out_"
                      render_endpoint="render_multi_inout_report"
                      @multiRecordCount="multiRecordCount"
                      :display_emp_pic="false"
                      :system_user_id="system_user_id"
                    />
                  </v-tab-item>
                </v-tabs-items>
              </v-tab-item>
              <v-tab-item>
                <ScheduleListVue
                  :id="table_id"
                  :employee_id="employee_id"
                  :system_user_id="system_user_id"
              /></v-tab-item>
              <v-tab-item>
                <v-card>
                  <v-card-title dense class="popup_title">
                    Leave Group : {{ leave_group_name }}
                  </v-card-title>
                  <v-card-text>
                    <v-data-table
                      v-model="ids"
                      item-key="id"
                      :headers="headersGroupInfo"
                      :items="DialogLeaveGroupData"
                      :loading="loading"
                      :hide-default-footer="true"
                      class="elevation-1"
                    >
                      <template v-slot:item.leave_type="{ item }" center>
                        {{ item.leave_type.name }} ({{
                          item.leave_type.short_name
                        }})
                      </template>
                      <template v-slot:item.total="{ item }">
                        <v-chip color="black" text-color="white">
                          {{ item.leave_type_count }}</v-chip
                        >
                      </template>
                      <template v-slot:item.approved="{ item }">
                        <v-chip color="primary">
                          {{ item.employee_used }}</v-chip
                        >
                      </template>
                      <template v-slot:item.available="{ item }">
                        <v-chip class="ma-2" color="green" text-color="white">
                          {{
                            item.leave_type_count - item.employee_used
                          }}</v-chip
                        >
                      </template>
                    </v-data-table>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <EmployeePayslips
                  :id="table_id"
                  :employee_id="employee_id"
                  :system_user_id="system_user_id"
              /></v-tab-item>
              <v-tab-item>
                <EmployeePersonal
                  :employeeObject="employeeObject"
                ></EmployeePersonal>
              </v-tab-item>
              <v-tab-item>
                <v-card :loading="loadingDeviceData">
                  <v-card-actions>
                    <!-- <span style="font-size: 20px">Mapped Devices</span>
                    <v-spacer></v-spacer> -->
                  </v-card-actions>

                  <v-card-text class="mt-2">
                    <v-card
                      v-for="(employee, index) in employeeUploadedDevicesInfo"
                      :key="'vs' + index"
                    >
                      <v-card-title style="font-size: 13px"
                        >{{ ++index }}: Device: {{ employee.deviceName }}
                      </v-card-title>
                      <v-card-text class="mt-2">
                        <v-row
                          class="100%"
                          style="margin: auto; line-height: 36px"
                          v-if="employee.SDKresponseData.data"
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
                                employee.SDKresponseData.data.faceImage
                                  ? 'data:image/jpeg;base64, ' +
                                    employee.SDKresponseData.data.faceImage
                                  : '/no-profile-image.jpg'
                              "
                            >
                            </v-img>
                          </v-col>

                          <v-col cols="8" style="padding: 0px">
                            <v-simple-table>
                              <tr>
                                <td>Name</td>
                                <td>
                                  : {{ employee.SDKresponseData.data.name }}
                                </td>
                              </tr>

                              <tr>
                                <td>System User Id</td>
                                <td>
                                  : {{ employee.SDKresponseData.data.userCode }}
                                </td>
                              </tr>

                              <tr>
                                <td>Expiry Date</td>
                                <td>
                                  : {{ employee.SDKresponseData.data.expiry }}
                                </td>
                              </tr>

                              <tr>
                                <td>Timezone Group Id</td>
                                <td>
                                  :
                                  {{ employee.SDKresponseData.data.timeGroup }}
                                </td>
                              </tr>
                            </v-simple-table>
                          </v-col>
                        </v-row>

                        <div v-else>{{ employee.SDKresponseData.message }}</div>

                        <v-row>
                          <v-col cols="12">
                            <v-btn
                              class="align-right"
                              style="float: right; color: #fff"
                              dense
                              small
                              color="red"
                              @click="deleteFromDevice(employee)"
                              >Delete</v-btn
                            >
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs-items>
          </v-tabs>
        </v-card>
      </v-col>
      <v-col cols="2">
        <table></table>
      </v-col>
      <v-col cols="3" style="max-width: 20%">
        <table></table>
      </v-col>
      <v-col cols="2">
        <table></table>
      </v-col>
    </v-row>
    <v-navigation-drawer v-model="drawer" bottom temporary right fixed>
      <v-toolbar class="background" dense dark
        >Documents
        <v-spacer></v-spacer>

        <v-icon @click="drawer = false" outlined dark color="white">
          mdi mdi-close-circle
        </v-icon>
      </v-toolbar>
      <table style="width: 100%; border-collapse: collapse; margin: 5px">
        <thead>
          <tr>
            <th
              style="
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
              "
            >
              Title
            </th>
            <th
              style="
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
              "
            >
              Document
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(document, index) in document_list" :key="index">
            <td
              style="
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
              "
            >
              {{ document.title }}
            </td>
            <td
              style="
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
              "
            >
              <a :href="document.attachment" download target="_blank">
                <v-icon color="primary"> mdi-download </v-icon>
              </a>
            </td>
          </tr>
          <!-- Add more rows as needed -->
        </tbody>
      </table>
    </v-navigation-drawer>
  </div>
  <div v-else>
    <v-row v-if="!employeeObject">
      <ComonPreloader icon="face-scan" />
    </v-row>
  </div>
</template>
<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

import AttendanceReport from "../../components/attendance_report/reportComponent.vue";

import EmployeePersonal from "../../components/EmployeesLogin/EmployeePersonal.vue";
import EmployeePayslips from "../../components/EmployeesLogin/EmployeePayslips.vue";
import generalHeaders from "../../headers/general.json";
import multiHeaders from "../../headers/multi.json";
import doubleHeaders from "../../headers/double.json";

import ScheduleListVue from "../../components/EmployeesLogin/EmployeeScheduleList.vue";
export default {
  props: ["system_user_id", "employee_id", "table_id"],
  components: {
    VueCropper,
    AttendanceReport,
    EmployeePayslips,
    EmployeePersonal,
    ScheduleListVue,
  },
  data: () => ({
    loadingDeviceData: false,
    employeeUploadedDevicesInfo: [],
    tab2: "",
    leave_group_name: "",
    absents: 0,
    leaves: 0,
    presents: 0,
    avg_clock_in: "00:00",
    avg_clock_out: "00:00",
    avg_working_hours: "00:00",
    tab: "",
    generalHeaders,
    multiHeaders,
    doubleHeaders,
    report_template: "Template1",
    month_year_display: "",
    calender_month_switcher: 0,
    tabmain: null,
    employeeObject: null,
    switchValue: true,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    selectedFile: "",
    upload_edit: {
      name: "",
    },
    drawer: false,
    group: null,
    attrs: [],
    dialog: false,
    editDialog: false,
    tab: null,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    tab: null,
    selectedItem: 1,

    on: "",
    color: "background",
    files: "",
    Model: "Employee",
    endpoint: "employee",
    search: "",
    loading: false,
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 8,
    response: "",
    ListName: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "",
      display_name: "",
      employee_id: "",
      system_user_id: "",
      profile_picture: "",
      employee_role_id: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    response: "",
    departments: [],
    sub_departments: [],
    designations: [],
    roles: [],
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    payloadOptions: {},
    last_login: {},
    document_list: [],

    payload11: {},
    tabSwitcherCount: 0,
    headersGroupInfo: [
      {
        text: "Leave Type",
        align: "left",
        key: "name",
        sortable: false,
        value: "leave_type",
      },
      {
        text: "Total",
        align: "center",
        key: "name",
        sortable: false,
        value: "total",
      },
      {
        text: "Approved",
        align: "center",
        key: "name",
        sortable: false,
        value: "approved",
      },
      {
        text: "Available",
        align: "center",
        key: "name",
        sortable: false,
        value: "available",
      },
    ],
    test: "",
    DialogLeaveGroupData: [],
  }),

  created() {
    this.month_year_display = this.$dateFormat.format_month_name_year(
      new Date().toISOString(),
      0
    );
    if (!this.display_emp_pic) {
      this.generalHeaders = this.generalHeaders.filter(
        (e) => e.text != "Employee"
      );
      this.doubleHeaders = this.doubleHeaders.filter(
        (e) => e.text != "Employee"
      );
      this.multiHeaders = this.multiHeaders.filter((e) => e.text != "Employee");
    }
  },
  mounted() {
    this.getDataFromApi();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    // tab(val) {
    //   console.log(val);
    //   this.commonMethod();
    // },
    // options: {
    //   handler() {
    //     this.getDataFromApi();
    //   },
    //   deep: true,
    // },
    group() {
      this.drawer = false;
    },
  },
  computed: {
    formatJoiningDate() {
      let dateObj = new Date();

      let { joining_date } = this.employeeObject;

      if (joining_date) {
        dateObj = new Date(joining_date);
      }

      let day = dateObj.getDate();
      let month = dateObj.toLocaleString("default", { month: "long" });
      let year = dateObj.getFullYear();
      let daySuffix = this.setDaySuffix(day);
      return ` DOJ: ${day}<sup>${daySuffix}</sup> ${month} ${year} `;
    },
  },
  methods: {
    deleteFromDevice(item) {
      if (confirm("Are you sure want to Delete From This Device?")) {
        let options = {
          params: {
            company_id: this.$auth.user.company_id,

            system_user_id: item.system_user_id,

            device_id: item.device_id,
          },
        };
        this.$axios
          .post(`delete-employee-from-device`, options.params)
          .then(({ data }) => {
            this.response = "Employee Details are deleted from device";
            this.snackbar = true;
          });
      }
    },
    loadMappedDevicesList() {
      if (this.employeeUploadedDevicesInfo.length == 0) {
        this.viewUploadedemployeeInfo();
      }
    },
    async viewUploadedemployeeInfo(item) {
      this.uploadedUserInfoDialog = true;

      this.employeeUploadedDevicesInfo = [];
      this.loadingDeviceData = true;
      let counter = 1;
      const data = await this.$store.dispatch("fetchData", {
        key: "devices",

        refresh: true,
        endpoint: "device-list",
      });

      let devices = data.filter((d) => d.device_type != "Mobile");

      if (devices) {
        await devices.forEach((element) => {
          let options = {
            params: {
              company_id: this.$auth.user.company_id,

              system_user_id: this.employeeObject.system_user_id,

              device_id: element.device_id,
            },
          };
          this.loadingDeviceData = true;
          this.$axios
            .get(`get-device-person-details`, options)
            .then(({ data }) => {
              if (devices.length == counter) {
                this.loadingDeviceData = false;
              }
              counter++;
              if (!data.deviceName) {
                this.response = data.message;
                this.snackbar = true;

                return;
              } else {
                data.system_user_id = this.employeeObject.system_user_id;
                data.device_id = element.device_id;

                this.employeeUploadedDevicesInfo.push(data);

                return;
              }
            });
        });
      }
    },
    // getDonwloadLink(pic, employee_id) {
    //   return (
    //     process.env.BACKEND_URL + "/download-emp-pic/" + pic + "/" + employee_id
    //   );
    // },
    getDonwloadLink(pic, employee_id) {
      return (
        process.env.BACKEND_URL +
        "/download-employee-profile-pdf/" +
        employee_id
      );
    },

    gotoGroupDetails(leaveGroupId, employee_id, employee_name) {
      if (!leaveGroupId) {
        return false;
      }
      this.dialogLeaveGroup = true;
      this.viewEmployeeName = employee_name;
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          employee_id: employee_id,
        },
      };
      this.$axios
        .get("leave_groups/" + leaveGroupId, options)
        .then(({ data }) => {
          this.DialogLeaveGroupData = data[0].leave_count;
          this.leave_group_name = data[0].group_name;
        });
    },
    getAvgClock(payload11) {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          system_user_id: this.system_user_id, //this.$auth.user.employee.system_user_id,
          start_date: payload11.from_date,
          end_date: payload11.to_date,
        },
      };

      this.$axios.get("attendance_avg_clock", options).then(({ data }) => {
        try {
          this.avg_clock_in = data.avg_clock_in;
          this.avg_clock_out = data.avg_clock_out;

          this.avg_working_hours = data.avg_working_hours;
          this.absents = data.leaves.total_absent;
          this.leaves = data.leaves.total_leaves;
          this.presents = data.leaves.total_present;
        } catch (e) {}
      });
    },
    commonMethod() {
      let date = this.addMonths(
        new Date(),
        this.calender_month_switcher
      ).toISOString();
      let monthObj = this.$dateFormat.monthStartEnd(date);
      if (this.employeeObject) {
        this.payload11 = {
          company_id: this.$auth.user.company_id,
          report_type: "Monthly", //filterDay,

          overtime: 0,
          from_date: monthObj.first,
          to_date: monthObj.last,
          employee_id: this.employeeObject.system_user_id,

          filterType: "Monthly",
        };
        this.getAvgClock(this.payload11);
        if (this.tabSwitcherCount == 0) {
          setTimeout(() => {
            this.tab = "tab-2";
          }, 2000);
          setTimeout(() => {
            this.tab = "tab-3";
          }, 3000);
          setTimeout(() => {
            this.tab = "tab-1";
          }, 4000);
          this.tabSwitcherCount++;
        }
      }
    },
    genRecordCount(recordCount) {
      // if (recordCount > 0) {
      //   this.tab = "tab-1";
      // }
    },
    dualRecordCount(recordCount) {
      // if (recordCount > 0) {
      //   this.tab = "tab-2";
      // }
    },

    multiRecordCount(recordCount) {
      // if (recordCount > 0) {
      //   this.tab = "tab-3";
      // }
    },

    addMonths(date, months) {
      date.setMonth(date.getMonth() + months);
      return date;
    },
    updateCalender(counter) {
      //this.tabSwitcherCount = 0;
      console.log("counter", counter);
      let date = this.addMonths(new Date(), counter).toISOString();
      this.month_year_display = this.$dateFormat.format_month_name_year(date);
      this.commonMethod();
    },
    getDataFromApi() {
      let options = {
        params: {
          id: this.table_id, //this.$auth.user.employee.id,
          company_id: this.$auth.user.company_id,
          // department_id: this.department_filter_id,
        },
      };

      this.$axios.get("employee", options).then(({ data }) => {
        console.log("datadata", data);
        if (data.data[0]) this.employeeObject = data.data[0];
        if (this.employeeObject && this.employeeObject.leave_group_id)
          this.gotoGroupDetails(
            this.employeeObject.leave_group_id,
            this.employeeObject.employee_id,
            this.employeeObject.first_name
          );
        setTimeout(() => {
          this.commonMethod();

          this.getLastLogin();
        }, 2000);
      });
    },
    getLastLogin() {
      //

      if (this.employeeObject)
        this.$axios
          .get(
            `activity/${this.employeeObject.user_id}?action=Login&company_id=${this.$auth.user.company_id}`
          )
          .then(({ data }) => {
            this.last_login = data.date_time;
          });
    },

    openDocumentDrawer() {
      //this.drawer = true;
      this.$axios
        .get(`documentinfo/${this.employeeObject.id}`)
        .then(({ data }) => {
          this.document_list = data;
        });
    },
    formatDate(date) {
      let dateObj = new Date();

      if (date) {
        dateObj = new Date(date);
      }

      let day = dateObj.getDate();
      let month = dateObj.getMonth() + 1;
      let year = dateObj.getFullYear();
      return `${day}/${month}/${year}`;
    },
    setDaySuffix(day) {
      switch (day) {
        case 1:
        case 21:
        case 31:
          return "st";
          break;
        case 2:
        case 22:
          return "nd";
          break;
        case 3:
        case 23:
          return "rd";
          break;
        default:
          return "th";
          break;
      }
    },
    getDepartments() {
      this.$axios.get(`departments`, this.payloadOptions).then(({ data }) => {
        this.departments = data.data;
      });
    },
    getSubDepartments() {
      this.$axios
        .get(`sub-departments`, this.payloadOptions)
        .then(({ data }) => {
          this.sub_departments = data.data;
        });
    },
    getDesignations() {
      this.$axios.get(`designation`, this.payloadOptions).then(({ data }) => {
        this.designations = data.data;
      });
    },
    getRoles() {
      this.payloadOptions.params.role_type = "employee";

      this.$axios.get(`role`, this.payloadOptions).then(({ data }) => {
        this.roles = data.data;
      });
    },
    can() {
      return true;
    },
    close() {
      this.dialog = false;
    },
  },
};
</script>

<!-- <style scoped>
  .v-slide-group__content {
    background-color: #ddd;
  }
  </style> -->
