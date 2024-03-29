<template>
  <div
    style="width: 100% !important"
    v-if="can(`employee_device_photo_upload`)"
  >
    <div class="text-center ma-2">
      <v-snackbar
        :color="snackbar.color"
        v-model="snackbar.show"
        small
        top="top"
        :timeout="3000"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <v-col cols="12">
        <!-- <Back class="primary white--text" /> -->
      </v-col>
      <v-col v-if="isCompany" cols="3">
        <v-select
          @change="filterDepartmentsByBranch($event)"
          v-model="branch_id"
          :items="[{ branch_name: `All Branches`, id: `` }, ...branchesList]"
          dense
          placeholder="All Branches"
          outlined
          item-value="id"
          item-text="branch_name"
        >
        </v-select>
      </v-col>
      <v-col cols="3">
        <v-select
          @change="loadDepartmentemployees"
          v-model="departmentselected"
          :items="departments"
          dense
          outlined
          item-value="id"
          item-text="name"
          hide-details
          label="Department"
          :search-input.sync="searchInput"
        ></v-select>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="5">
        <v-card class="photo-displaylist" style="height: 300px">
          <v-toolbar color=" " dense flat style="border: 1px solid #ddd">
            <span> Employees </span>
          </v-toolbar>
          <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden">
            <v-card-text>
              <v-row
                class="timezone-displaylistview1"
                v-for="(user, index) in leftEmployees"
                :id="user.id"
                v-model="leftEmployees"
                :key="user.id"
                style="border-bottom: 1px solid #ddd"
              >
                <v-col md="1" style="padding: 0px; margin-top: -7px">
                  <v-checkbox
                    dense
                    small
                    hideDetails
                    v-model="leftSelectedEmp"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                  <!-- <v-checkbox
                    style="padding: 0px"
                    v-else
                    dense
                    small
                    disabled
                    hideDetails
                    indeterminate
                    v-model="leftSelectedEmp"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox> -->
                </v-col>

                <v-col md="1" style="padding: 0px">
                  <v-img
                    class="employee-pic"
                    :title="user.first_name + ' ' + user.last_name"
                    style="float: left; border-radius: 50%; height: auto"
                    :src="
                      user.profile_picture
                        ? user.profile_picture
                        : '/no-profile-image.jpg'
                    "
                  >
                  </v-img>
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.first_name }}
                  {{ user.last_name }}
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.employee_id }}
                </v-col>
              </v-row>
            </v-card-text>

            <!-- <v-card-text
              class="photo-displaylistview"
              v-for="(user, index) in leftEmployees"
              :id="user.id"
              v-on:dblclick="
                (counter += 1), moveToRightEmp(user.id, user.timezone)
              "
              :key="index"
            >
              <v-row>
                <v-col class="col-1" style="padding: 0px">
                  <v-checkbox
                    v-if="user.profile_picture"
                    class="col"
                    v-model="leftSelectedEmp"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                  <v-checkbox
                    v-else
                    indeterminate
                    value
                    disabled
                    hide-details
                    class="col-1 d-flex flex-column justify-center"
                  ></v-checkbox>
                </v-col>
                <v-col col="2" class="col" style="padding-top: 21px">
                  {{ user.employee_id }}: {{ user.display_name }}
                </v-col>
                <v-col col=" 2">
                  <v-img
                    v-if="user.profile_picture != ''"
                    style="border-radius: 50%; width: 40px"
                    :src="user.profile_picture || '/no-profile-image.jpg'"
                  >
                  </v-img>
                </v-col>
              </v-row>
            </v-card-text> -->
          </div>
        </v-card>
      </v-col>

      <v-col cols="2">
        <div style="text-align: -webkit-center">
          <button
            type="button"
            id="undo_redo_undo"
            class="btn primary btn-block white--text"
          >
            Options
          </button>

          <button
            @click="moveToRightEmpOption2"
            type="button"
            id="undo_redo_rightSelected"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-right theme--red"
            ></i>
          </button>

          <button
            @click="allmoveToRightEmp"
            type="button"
            id="undo_redo_rightAll"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-double-right theme--red"
            ></i>
          </button>
          <button
            @click="moveToLeftempOption2"
            type="button"
            id="undo_redo_leftSelected"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-left theme--red"
            ></i>
          </button>
          <button
            @click="allmoveToLeftemp"
            type="button"
            id="undo_redo_leftAll"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-double-left theme--red"
            ></i>
          </button>
        </div>
      </v-col>

      <v-col cols="5">
        <v-card class="photo-displaylist" style="height: 300px">
          <v-toolbar color=" " dense flat style="border: 1px solid #ddd">
            <span>Selected Employees </span>
          </v-toolbar>
          <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden">
            <v-card-text>
              <v-row
                class="timezone-displaylistview1"
                v-for="(user, index) in rightEmployees"
                :id="user.id"
                v-model="rightSelectedEmp"
                :key="user.id"
                style="border-bottom: 1px solid #ddd"
              >
                <v-col md="1" style="padding: 0px">
                  <v-checkbox
                    dense
                    small
                    hideDetails
                    v-model="rightSelectedEmp"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                  <!-- <v-checkbox  v-if="user.profile_picture"
                    style="padding: 0px"
                    v-else
                    dense
                    small
                    hideDetails
                    v-model="rightSelectedEmp"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox> -->
                </v-col>

                <v-col md="1" style="padding: 0px">
                  <v-img
                    class="employee-pic"
                    :title="user.first_name + ' ' + user.last_name"
                    style="float: left; border-radius: 50%; height: auto"
                    :src="
                      user.profile_picture
                        ? user.profile_picture
                        : '/no-profile-image.jpg'
                    "
                  >
                  </v-img>
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.first_name }}
                  {{ user.last_name }}
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.employee_id }}
                </v-col>
              </v-row>
            </v-card-text>
            <!-- <v-card-text
              class="photo-displaylistview"
              v-for="(user, index) in rightEmployees"
              :id="user.id"
              v-model="rightSelectedEmp"
              :key="user.id"
            >
              <div class="row">
                <v-col class="col-1" style="padding: 0px">
                  <v-checkbox
                    hideDetails
                    class="col-1 d-flex flex-column justify-center"
                    v-model="rightSelectedEmp"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                </v-col>
                <v-col col="2" class="col" style="padding-top: 21px">
                  {{ user.employee_id }} : {{ user.display_name }}
                </v-col>
                <v-col col="4">
                  <span
                    ><v-img
                      v-if="user.profile_picture != ''"
                      style="border-radius: 50%; width: 40px"
                      :src="user.profile_picture"
                    >
                    </v-img
                  ></span>
                </v-col>
                <v-col col="4">
                  <span
                    v-if="user.sdkEmpResponse == 'Success'"
                    style="color: green"
                    >{{ user.sdkEmpResponse }}</span
                  >
                  <span v-else style="color: red">{{
                    user.sdkEmpResponse
                  }}</span>
                </v-col>
              </div>
            </v-card-text> -->

            <!-- <select
              multiple
              v-model="rightSelectedEmp"
              @dblclick="moveToLeftemp"
              class="form-control"
              size="13"
            >
              <option
                v-for="(user, index) in rightEmployees"
                :key="index"
                :value="user.id"
              >
                Eid: {{ user.employee_id }} : {{ user.display_name }} :
              </option>
            </select> -->
          </div>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="5">
        <v-card class="photo-displaylist" style="height: 300px">
          <v-toolbar color=" " dense flat style="border: 1px solid #ddd">
            <span> Devices</span>
          </v-toolbar>
          <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden">
            <v-card-text>
              <v-row
                class="timezone-displaylistview1"
                v-for="(user, index) in leftDevices"
                :id="user.id"
                v-model="leftSelectedDevices"
                :key="user.id"
                style="border-bottom: 1px solid #ddd"
              >
                <v-col md="1" style="padding: 0px;margin-top-3">
                  <v-checkbox
                    v-if="user.status.name == 'active'"
                    dense
                    small
                    hideDetails
                    v-model="leftSelectedDevices"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                  <v-checkbox
                    style="padding: 0px;margin-top-3"
                    v-else
                    indeterminate
                    value
                    disabled
                    dense
                    small
                    hideDetails
                    v-model="leftSelectedDevices"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.name }}
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.model_number }}
                </v-col>
                <v-col md="3" style="padding: 0px">
                  <img
                    title="Online"
                    v-if="user.status.name == 'active'"
                    src="/icons/device_status_open.png"
                    style="width: 30px"
                  />
                  <img
                    title="Offline"
                    v-else
                    src="/icons/device_status_close.png"
                    style="width: 30px"
                  />
                </v-col>
              </v-row>
            </v-card-text>
            <!-- <v-card-text
              class="photo-displaylistview"
              v-for="(user, index) in leftDevices"
              :id="user.id"
              v-model="leftSelectedDevices"
              :key="user.id"
            >
              <div class="row">
                <v-col class="col-1" style="padding: 0px">
                  <v-checkbox
                    v-if="user.status.name == 'active'"
                    hideDetails
                    class="col-1 d-flex flex-column justify-center"
                    v-model="leftSelectedDevices"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                  <v-checkbox
                    v-else
                    indeterminate
                    value
                    disabled
                    hide-details
                    class="col-1 d-flex flex-column justify-center"
                  ></v-checkbox>
                </v-col>
                <div col-4 class="col" style="padding-top: 21px">
                  {{ user.name }} : {{ user.device_id }}
                  <span
                    style="color: green"
                    v-if="user.status.name == 'active'"
                  >
                    Online</span
                  >
                  <span style="color: red" v-else>Offline </span>
                </div>
              </div>
            </v-card-text> -->
          </div>
        </v-card>
      </v-col>

      <v-col cols="2">
        <div style="text-align: -webkit-center">
          <button
            type="button"
            id="undo_redo_undo"
            class="btn primary btn-block white--text"
          >
            Options
          </button>

          <button
            @click="moveToRightDevicesOption2"
            type="button"
            id="undo_redo_rightSelected"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-right theme--red"
            ></i>
          </button>

          <button
            @click="allmoveToRightDevices"
            type="button"
            id="undo_redo_rightAll"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-double-right theme--red"
            ></i>
          </button>
          <button
            @click="moveToLeftDevicesOption2"
            type="button"
            id="undo_redo_leftSelected"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-left theme--red"
            ></i>
          </button>
          <button
            @click="allmoveToLeftDevices"
            type="button"
            id="undo_redo_leftAll"
            class="btn btn-default btn-block"
          >
            <i
              aria-hidden="true"
              class="v-icon notranslate mdi mdi-chevron-double-left theme--red"
            ></i>
          </button>
        </div>
      </v-col>

      <v-col cols="5">
        <v-card class="photo-displaylist" style="height: 300px">
          <v-toolbar color=" " dense flat style="border: 1px solid #ddd">
            <span>Selected Devices</span>
          </v-toolbar>
          <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden">
            <v-card-text>
              <v-row
                class="timezone-displaylistview1"
                v-for="(user, index) in rightDevices"
                :id="user.id"
                v-model="rightSelectedDevices"
                :key="user.id"
                style="border-bottom: 1px solid #ddd"
              >
                <v-col md="1" style="padding: 0px;margin-top-3">
                  <v-checkbox
                    v-if="user.status.name == 'active'"
                    dense
                    small
                    hideDetails
                    v-model="rightSelectedDevices"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                  <v-checkbox
                    style="padding: 0px;margin-top-3"
                    v-else
                    indeterminate
                    value
                    disabled
                    dense
                    small
                    hideDetails
                    v-model="leftSelectedDevices"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.name }}
                </v-col>
                <v-col md="3" style="padding: 0px; padding-top: 5px">
                  {{ user.model_number }}
                </v-col>
                <v-col md="3" style="padding: 0px">
                  <span
                    v-if="user.sdkDeviceResponse == 'Success'"
                    style="color: green"
                    >{{ user.sdkDeviceResponse }}</span
                  >
                  <span v-else style="color: red">{{
                    user.sdkDeviceResponse
                  }}</span>
                </v-col>
              </v-row>
            </v-card-text>
            <!-- <v-card-text
              class="photo-displaylistview"
              v-for="(user, index) in rightDevices"
              :id="user.id"
              v-model="rightSelectedDevices"
              :key="user.id"
            >
              <div class="row">
                <v-col class="col-1" style="padding: 0px">
                  <v-checkbox
                    hideDetails
                    class="col-1 d-flex flex-column justify-center"
                    v-model="rightSelectedDevices"
                    :value="user.id"
                    primary
                    hide-details
                  ></v-checkbox>
                </v-col>
                <div col class="col-sm" style="padding-top: 21px">
                  {{ user.name }} : {{ user.device_id }}
                </div>
                <div col class="col-sm d-flex flex-column justify-center">
                  <span
                    v-if="user.sdkDeviceResponse == 'Success'"
                    style="color: green"
                    >{{ user.sdkDeviceResponse }}</span
                  >
                  <span v-else style="color: red">{{
                    user.sdkDeviceResponse
                  }}</span>
                </div>
              </div>
            </v-card-text> -->
          </div>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-progress-linear
        v-if="progressloading"
        :active="loading"
        :indeterminate="loading"
        absolute
        color="primary"
      ></v-progress-linear>
      <v-col cols="12">
        <div class="row">
          <div class="col col-lg-6 text-center">
            <span v-if="errors && errors.message" class="text-danger mt-2">{{
              errors.message
            }}</span>
          </div>
          <div class="col col-lg-3 text-right">
            <div style="width: 150px; float: right">
              <button
                :loading="loading"
                @click="goback()"
                type="button"
                id="save"
                class="btn primary btn-block white--text v-size--default"
              >
                Back
              </button>
            </div>
          </div>
          <div class="col col-lg-3 text-right">
            <div style="width: 150px; float: right">
              <button
                :disabled="!displaybutton"
                :loading="loading"
                @click="onSubmit"
                type="button"
                id="save"
                class="btn primary btn-block white--text v-size--default"
              >
                Submit
              </button>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
// import Back from "../components/Snippets/Back.vue";

export default {
  components: {},
  data() {
    return {
      isCompany: true,
      branch_id: null,
      branchesList: [],
      loading: false,
      counter: 0,
      devices_dialog: [],
      displaybutton: false,
      progressloading: false,
      searchInput: "",
      snackbar: {
        message: "",
        color: "black",
        show: false,
      },
      errors: [],
      response: "",
      color: "primary",
      loading: true,
      endpointEmployee: "get_employeeswith_timezonename",
      endpointUpdatetimezoneStore: "employee_timezone_mapping",
      //endpointUpdatetimezoneUpdate: "employee_timezone_mapping",
      endpointDevise: "device",
      leftSelectedEmp: [],
      departmentselected: [],
      departments: [],
      leftEmployees: [],
      rightSelectedEmp: [],
      rightEmployees: [],
      leftSelectedDevices: [],
      leftDevices: [],
      rightSelectedDevices: [],
      rightDevices: [],
      department_ids: ["---"],
      timezones: ["Timeszones are not available"],
      timezonesselected: [],
      options: {
        params: {
          company_id: this.$auth.user.company_id,
          cols: ["id", "name"],
        },
      },
    };
  },
  mounted: function () {
    // this.snackbar.show = false;
    // this.snackbar.message = "Data loading...Please wait ";
    // this.response = "Data loading...Please wait ";

    this.$nextTick(function () {
      setTimeout(() => {
        this.loading = false;
        //this.snackbar = false;
      }, 2000);
    });

    setTimeout(() => {
      this.loading = false;
      //this.snackbar = false;
    }, 2000);
  },
  async created() {
    if (this.$auth.user.branch_id == 0) {
      this.isCompany = true;
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
    } else {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
    }
    this.progressloading = true;
    await this.filterDepartmentsByBranch(this.branch_id);
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    async filterDepartmentsByBranch(branch_id) {
      await this.getDepartmentsApi(this.options, branch_id);
      await this.getDevisesDataFromApi(branch_id);
      await this.getEmployeesDataFromApi(branch_id);
      await this.getTimezonesFromApi(branch_id);
    },
    fetch_logs() {},
    loadDepartmentemployees() {
      //this.loading = true;
      // let page = this.pagination.current;
      let url = this.endpointEmployee;
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          department_id: this.departmentselected,
          branch_id: this.branch_id,
          cols: ["id", "employee_id", "first_name", "last_name"],
        },
      };
      let page = 1;

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.leftEmployees = [];
        this.leftEmployees = data.data;
        this.leftSelectedEmp = [];

        this.rightEmployees = [];
        this.rightSelectedEmp = [];
      });
    },
    getDepartmentsApi(options, branch_id) {
      options.params.branch_id = branch_id;
      this.progressloading = true;
      this.$axios
        .get("departments", options)
        .then(({ data }) => {
          this.departments = data.data;
          this.departments.unshift({ id: "---", name: "All Departments" });
        })
        .catch((err) => console.log(err));
    },
    getTimezonesFromApi(branch_id) {
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          branch_id: branch_id,
        },
      };
      this.$axios
        .get("timezone", options)
        .then(({ data }) => {
          this.timezones = data.data;

          this.$axios
            .get("employee_timezone_mapping", options)
            .then(({ data }) => {
              data.data.forEach((element) => {
                let selectedindex = this.timezones.findIndex(
                  (e) => e.timezone_id == element.timezone_id
                );

                if (selectedindex >= 0) this.timezones.splice(selectedindex, 1);
              });
            });
        })
        .catch((err) => console.log(err));
    },
    resetErrorMessages() {
      this.errors = [];
      this.response = "";

      // $.extend(this.rightEmployees, {
      //   sdkEmpResponse: "",
      // });
      // $.extend(this.rightDevices, {
      //   sdkDeviceResponse: "",
      // });
      this.leftEmployees.forEach((element) => {
        element["sdkEmpResponse"] = "";
      });
      this.leftDevices.forEach((element) => {
        element["sdkDeviceResponse"] = "";
      });
    },

    // onSubmit_old() {
    //   this.resetErrorMessages();

    //   if (this.timezonesselected == "") {
    //     this.response = this.response + "Timezones not selected";
    //   } else if (this.rightEmployees.length == 0) {
    //     this.response = this.response + " Atleast select one Employee Details";
    //   } else if (this.rightDevices.length == 0) {
    //     this.response = this.response + " Atleast select one Device Details";
    //   }

    //   if (this.response != "") {
    //     this.snackbar.show = true;
    //     this.snackbar.message = this.response;
    //     this.snackbar.color = "red";
    //     setTimeout(() => {
    //       this.snackbar.show = false;
    //     }, 1000 * 10);
    //     return false;
    //   }
    //   this.loading = true;

    //   let columnsToFilter = ["systeM_user_id"];
    //   let onlyUserSystemids = {};
    //   // $.each(columnsToFilter, function (index, column) {
    //   //   if (this.timezonesselected.hasOwnProperty(column)) {
    //   //     onlyUserSystemids[column] = jsonData[column];
    //   //   }
    //   // });

    //   // Define the keys you want to select
    //   let keysToSelect = ["system_user_id"];

    //   // Select the specified keys from each object
    //   let filteredDataEmp = [];
    //   this.rightEmployees.map(function (obj) {
    //     let selectedObj = {};
    //     keysToSelect.forEach(function (key) {
    //       if (obj.hasOwnProperty(key)) {
    //         // selectedObj[key] = obj[key];
    //         selectedObj = obj[key];
    //         filteredDataEmp.push(selectedObj);
    //       }
    //     });
    //     return selectedObj;
    //   });
    //   //
    //   // Define the keys you want to select
    //   keysToSelect = ["device_id"];

    //   // Select the specified keys from each object
    //   let filteredDataDevices = [];
    //   this.rightDevices.map(function (obj) {
    //     let selectedObj = {};
    //     keysToSelect.forEach(function (key) {
    //       if (obj.hasOwnProperty(key)) {
    //         // selectedObj[key] = obj[key];
    //         selectedObj = obj[key];
    //         filteredDataDevices.push(selectedObj);
    //       }
    //     });
    //     return selectedObj;
    //   });

    //   let options = {
    //     timezone_id: this.timezonesselected,
    //     employee_id: this.rightEmployees,
    //     device_id: this.rightDevices,
    //     company_id: this.$auth.user.company_id,
    //     employee_ids: filteredDataEmp,
    //     device_ids: filteredDataDevices,
    //   };

    //   let url = this.endpointUpdatetimezoneStore;

    //   this.progressloading = true;
    //   let jsrightEmployees = this.rightEmployees;

    //   this.snackbar.show = true;
    //   this.response = "Connecting to devices... Please wait...";

    //   let SDKSuccessStatus = true;
    //   this.$axios.post(`${url}`, options).then(({ data }) => {
    //     this.displaybutton = false;
    //     if (data.record.SDKResponse.data) {
    //       this.loading = false;

    //       this.rightDevices.forEach((rightDevicesobj) => {
    //         // $.each(this.rightDevices, function (index, rightDevicesobj) {
    //         let SdkResponseDeviceobject = data.record.SDKResponse.data.find(
    //           (e) => e.sn == rightDevicesobj.device_id
    //         );

    //         let deviceStatusResponse = "";
    //         let EmpStatusResponse = "";

    //         if (SdkResponseDeviceobject.message == "") {
    //           deviceStatusResponse = "Success";
    //         } else if (
    //           SdkResponseDeviceobject.message == "The device was not found"
    //         ) {
    //           deviceStatusResponse = "The device was not found or offline";
    //           SDKSuccessStatus = false;
    //         } else if (SdkResponseDeviceobject.message == "person info error") {
    //           let SDKUseridArray = SdkResponseDeviceobject.userList; //SDK error userslist
    //           jsrightEmployees.forEach((element) => {
    //             let systemUserid = element.system_user_id;
    //             SDKSuccessStatus = false;
    //             let selectedEmpobject = SDKUseridArray.find(
    //               (e) => e.userCode == systemUserid
    //             );
    //             EmpStatusResponse = SdkResponseDeviceobject.sdkEmpResponse;
    //             deviceStatusResponse = "";

    //             if (EmpStatusResponse != "") {
    //               //Adding extra parameters for Employee object
    //               if (selectedEmpobject) {
    //                 element.sdkEmpResponse = "person info error ";
    //                 // $.extend(element, {
    //                 //   sdkEmpResponse: "person info error ",
    //                 // });
    //               } else {
    //                 element.sdkEmpResponse = "Success ";
    //                 // $.extend(element, {
    //                 //   sdkEmpResponse: "Success",
    //                 // });
    //               }
    //             }
    //           });
    //         } else {
    //         }

    //         //Adding extra parameters for Devices object
    //         // $.extend(rightDevicesobj, {
    //         //   sdkDeviceResponse:
    //         //     deviceStatusResponse != "" ? deviceStatusResponse : "Success",
    //         // });

    //         rightDevicesobj.forEach((element) => {
    //           element["sdkDeviceResponse"] =
    //             deviceStatusResponse != "" ? deviceStatusResponse : "Success";
    //         });
    //         this.errors = [];
    //       });
    //       this.rightEmployees = jsrightEmployees;
    //       this.progressloading = false;

    //       this.loading = false;
    //       if (!SDKSuccessStatus) {
    //         {
    //           this.errors = data.errors;
    //         }
    //         this.errors = [];
    //         this.errors["message"] =
    //           "Device/Employee Error:   Device and Employee details are Mapped. You can add/remove items from Edit list ";

    //         //this.displaybutton = false;
    //       } else {
    //         this.$router.push("/timezonemapping/list");
    //       }
    //     } else {
    //       this.errors = [];
    //       this.progressloading = false;

    //       this.errors["message"] = "Device Communication is not available";
    //       return false;
    //     }
    //   });
    // },
    goback() {
      this.$router.push("/timezonemapping/list");
    },
    getDevisesDataFromApi(branch_id, url = this.endpointDevise) {
      //this.loading = true;
      // let page = this.pagination.current;
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          sortBy: "status_id",
          branch_id: branch_id,
          //cols: ["id", "location", "name", "device_id", "status:id"],
        },
      };
      let page = 1;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.leftDevices = data.data;
      });
    },
    getEmployeesDataFromApi(branch_id, url = this.endpointEmployee) {
      //this.loading = true;
      // let page = this.pagination.current;
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          cols: [
            "id",
            "employee_id",
            "display_name",
            "first_name",
            "last_name",
          ],
          branch_id: branch_id,
        },
      };
      let page = 1;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.leftEmployees = data.data;
      }, 1000);
    },
    sortObject: (o) =>
      o.sort(function compareByName(a, b) {
        if (a.first_name && b.first_name) {
          let nameA = a.first_name.toUpperCase(); // Convert names to uppercase for case-insensitive sorting
          let nameB = b.first_name.toUpperCase();

          if (nameA < nameB) {
            return -1;
          } else if (nameA > nameB) {
            return 1;
          } else {
            return 0;
          }
        } else {
        }
      }),
    sortObjectD: (o) =>
      o.sort(function compareByName(a, b) {
        if (a.device_id && b.device_id) {
          let nameA = a.device_id.toUpperCase(); // Convert names to uppercase for case-insensitive sorting
          let nameB = b.device_id.toUpperCase();

          if (nameA < nameB) {
            return -1;
          } else if (nameA > nameB) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      }),
    sortObjectC: (o) =>
      o.sort(function compareByName(a, b) {
        if (a.name && b.name) {
          let nameA = a.name.toUpperCase(); // Convert names to uppercase for case-insensitive sorting
          let nameB = b.name.toUpperCase();

          if (nameA < nameB) {
            return -1;
          } else if (nameA > nameB) {
            return 1;
          } else {
            return 0;
          }
        }
      }),
    verifySubmitButton() {
      if (this.rightEmployees.length > 0 && this.rightDevices.length > 0) {
        this.displaybutton = true;
      } else {
        this.displaybutton = false;
      }
    },
    allmoveToLeftemp() {
      this.resetErrorMessages();
      this.leftEmployees = this.leftEmployees.concat(this.rightEmployees);
      this.rightEmployees = [];
      this.leftEmployees = this.sortObject(this.leftEmployees);

      this.verifySubmitButton();
    },
    allmoveToRightEmp() {
      this.resetErrorMessages();
      // this.rightEmployees = this.rightEmployees.concat(this.leftEmployees);
      // this.leftEmployees = [];

      this.rightEmployees = this.rightEmployees.concat(
        this.leftEmployees.filter((el) => el.profile_picture != null)
      );

      this.leftEmployees = this.leftEmployees.filter(
        (el) => el.profile_picture == null
      );

      this.rightEmployees = this.sortObject(this.rightEmployees);
      this.verifySubmitButton();
    },
    moveToLeftempOption2() {
      this.resetErrorMessages();

      if (!this.rightSelectedEmp.length) return;
      //for (let i = this.leftSelectedEmp.length; i > 0; i--) {
      let _rightSelectedEmp_length = this.rightSelectedEmp.length;
      for (let i = 0; i < _rightSelectedEmp_length; i++) {
        if (this.rightSelectedEmp) {
          let selectedindex = this.rightEmployees.findIndex(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          let selectedobject = this.rightEmployees.find(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          selectedobject.sdkEmpResponse = "";
          this.leftEmployees.push(selectedobject);

          this.rightEmployees.splice(selectedindex, 1);
        }
      }
      this.leftEmployees = this.sortObject(this.leftEmployees);
      for (let i = 0; i < _rightSelectedEmp_length; i++) {
        this.rightSelectedEmp.pop(this.rightSelectedEmp[i]);
      }

      this.verifySubmitButton();
    },
    moveToLeftemp(id) {
      this.resetErrorMessages();
      this.rightSelectedEmp.push(id);
      if (!this.rightSelectedEmp.length) return;

      //for (let i = this.leftSelectedEmp.length; i > 0; i--) {
      let _rightSelectedEmp_length = this.rightSelectedEmp.length;
      for (let i = 0; i < _rightSelectedEmp_length; i++) {
        if (this.rightSelectedEmp) {
          let selectedindex = this.rightEmployees.findIndex(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          let selectedobject = this.rightEmployees.find(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          selectedobject.sdkEmpResponse = "";
          this.leftEmployees.push(selectedobject);

          this.rightEmployees.splice(selectedindex, 1);
        }
      }
      this.leftEmployees = this.sortObject(this.leftEmployees);

      this.rightSelectedEmp.pop(id);
      this.verifySubmitButton();
    },
    check: function (id, e) {},
    selectLeftEmployee(id) {
      this.leftSelectedEmp.push(id);
    },

    moveToRightEmpOption2() {
      this.resetErrorMessages();
      this.resetErrorMessages();
      if (!this.leftSelectedEmp.length) return;

      let _leftSelectedEmp_length = this.leftSelectedEmp.length;
      for (let i = 0; i < _leftSelectedEmp_length; i++) {
        if (this.leftSelectedEmp) {
          let selectedindex = this.leftEmployees.findIndex(
            (e) => e.id == this.leftSelectedEmp[i]
          );

          let selectedobject = this.leftEmployees.find(
            (e) => e.id == this.leftSelectedEmp[i]
          );

          this.rightEmployees.push(selectedobject);

          this.leftEmployees.splice(selectedindex, 1);
        }
      }
      this.rightEmployees = this.sortObject(this.rightEmployees);

      for (let i = 0; i < _leftSelectedEmp_length; i++) {
        this.leftSelectedEmp.pop(this.leftSelectedEmp[i]);
      }
      this.verifySubmitButton();
    },

    /* Devices---------------------------------------- */
    allmoveToLeftDevices() {
      this.resetErrorMessages();
      this.leftDevices = this.leftDevices.concat(this.rightDevices);
      this.rightDevices = [];

      this.leftDevices = this.sortObjectD(this.leftDevices);
      this.verifySubmitButton();
    },
    allmoveToRightDevices() {
      this.resetErrorMessages();
      ///this.rightDevices = this.rightDevices.concat(this.leftDevices);
      //this.leftDevices = [];

      this.rightDevices = this.rightDevices.concat(
        this.leftDevices.filter((el) => el.status.name == "active")
      );

      this.leftDevices = this.leftDevices.filter(
        (el) => el.status.name == "inactive"
      );

      this.rightDevices = this.sortObjectD(this.rightDevices);
      this.verifySubmitButton();
    },
    moveToLeftDevicesOption2() {
      this.resetErrorMessages();

      if (!this.rightSelectedDevices.length) return;

      //for (let i = this.leftSelectedDevices.length; i > 0; i--) {
      let _rightSelectedDevices_length = this.rightSelectedDevices.length;
      for (let i = 0; i < _rightSelectedDevices_length; i++) {
        if (this.rightSelectedDevices) {
          let selectedindex = this.rightDevices.findIndex(
            (e) => e.id == this.rightSelectedDevices[i]
          );

          let selectedobject = this.rightDevices.find(
            (e) => e.id == this.rightSelectedDevices[i]
          );
          selectedobject["sdkEmpResponse"] = "";
          this.leftDevices.push(selectedobject);

          this.rightDevices.splice(selectedindex, 1);
        }
      }

      this.leftDevices = this.sortObjectD(this.leftDevices);

      for (let i = 0; i < _rightSelectedDevices_length; i++) {
        this.rightSelectedDevices.pop(this.rightSelectedDevices[i]);
      }
      this.verifySubmitButton();
    },
    moveToLeftDevices(id) {
      this.resetErrorMessages();
      this.rightSelectedDevices.push(id);

      if (!this.rightSelectedDevices.length) return;

      //for (let i = this.leftSelectedDevices.length; i > 0; i--) {
      let _rightSelectedDevices_length = this.rightSelectedDevices.length;
      for (let i = 0; i < _rightSelectedDevices_length; i++) {
        if (this.rightSelectedDevices) {
          let selectedindex = this.rightDevices.findIndex(
            (e) => e.id == this.rightSelectedDevices[i]
          );

          let selectedobject = this.rightDevices.find(
            (e) => e.id == this.rightSelectedDevices[i]
          );

          this.leftDevices.push(selectedobject);

          this.rightDevices.splice(selectedindex, 1);
        }
      }

      this.leftDevices = this.sortObjectD(this.leftDevices);

      this.rightSelectedDevices.pop(id);
      this.verifySubmitButton();
    },
    moveToRightDevicesOption2() {
      this.resetErrorMessages();

      if (!this.leftSelectedDevices.length) return;

      let _leftSelectedDevices_length = this.leftSelectedDevices.length;
      for (let i = 0; i < _leftSelectedDevices_length; i++) {
        if (this.leftSelectedDevices) {
          let selectedindex = this.leftDevices.findIndex(
            (e) => e.id == this.leftSelectedDevices[i]
          );

          let selectedobject = this.leftDevices.find(
            (e) => e.id == this.leftSelectedDevices[i]
          );
          selectedobject["sdkDeviceResponse"] = "";
          this.rightDevices.push(selectedobject);

          this.leftDevices.splice(selectedindex, 1);
        }
      }

      this.rightDevices = this.sortObjectD(this.rightDevices);

      for (let i = 0; i < _leftSelectedDevices_length; i++) {
        this.leftSelectedDevices.pop(this.leftSelectedDevices[i]);
      }
      this.verifySubmitButton();
    },
    moveToRightDevices(id) {
      this.resetErrorMessages();
      this.leftSelectedDevices.push(id);

      if (!this.leftSelectedDevices.length) return;

      let _leftSelectedDevices_length = this.leftSelectedDevices.length;
      for (let i = 0; i < _leftSelectedDevices_length; i++) {
        if (this.leftSelectedDevices) {
          let selectedindex = this.leftDevices.findIndex(
            (e) => e.id == this.leftSelectedDevices[i]
          );

          let selectedobject = this.leftDevices.find(
            (e) => e.id == this.leftSelectedDevices[i]
          );

          selectedobject["sdkDeviceResponse"] = "";
          this.rightDevices.push(selectedobject);

          this.leftDevices.splice(selectedindex, 1);
        }
      }

      this.rightDevices = this.sortObjectD(this.rightDevices);

      this.leftSelectedDevices.pop(id);
      this.verifySubmitButton();
    },
    // onSubmit_old() {
    //   this.resetErrorMessages();
    //   this.displaybutton = false;
    //   this.loading = true;
    //   if (this.rightEmployees.length == 0) {
    //     this.response = this.response + " Atleast select one Employee Details";
    //   } else if (this.rightDevices.length == 0) {
    //     this.response = this.response + " Atleast select one Device Details";
    //   }

    //   this.loading_dialog = true;
    //   this.errors = [];
    //   let personListArray = [];
    //   this.rightEmployees.forEach(async (item) => {
    //     let person = {
    //       name: item.first_name + " " + item.last_name,
    //       userCode: parseInt(item.system_user_id),

    //       //faceImage: `https://stagingbackend.ideahrms.com/media/employee/profile_picture/1686381362.jpg?t=786794`,
    //       faceImage: item.profile_picture,
    //     };
    //     if (item.rfid_card_number != "") {
    //       person.cardData = item.rfid_card_number;
    //     }
    //     if (item.rfid_card_password != "") {
    //       person.password = item.rfid_card_password;
    //     }
    //     personListArray.push(person);
    //   });

    //   this.rightDevices.forEach(async (item) => {
    //     // let person = {
    //     //   name: item.display_name,
    //     //   userCode: parseInt(item.system_user_id),

    //     //   //faceImage: `https://stagingbackend.ideahrms.com/media/employee/profile_picture/1686381362.jpg?t=786794`,
    //     //   faceImage: item.profile_picture
    //     // };
    //     // personListArray.push(person);

    //     let payload = {
    //       personList: personListArray,
    //       snList: [item.device_id],
    //     };

    //     if (payload.snList && payload.snList.length === 0) {
    //       alert(`Atleast one device must be selected`);
    //       return false;
    //     }

    //     this.devices_dialog.forEach((e) => {
    //       e.state = "---";
    //       e.message = "---";
    //     });

    //     //try {
    //     const { data } = await this.$axios.post(`/Person/AddRange`, payload);

    //     if (data.status == 200) {
    //       this.loading_dialog = false;

    //       this.snackbar.show = true;
    //       this.response = "Employee(s) pictures has been uploaded";

    //       let jsrightEmployees = this.rightEmployees;
    //       let SDKSuccessStatus = true;
    //       this.rightDevices.forEach((elementDevice) => {
    //         let SdkResponseDeviceobject = data.data.find(
    //           (e) => e.sn == elementDevice.device_id
    //         );

    //         let deviceStatusResponse = "";
    //         let EmpStatusResponse = "";

    //         if (SdkResponseDeviceobject.message == "") {
    //           deviceStatusResponse = "Success";
    //         } else if (
    //           SdkResponseDeviceobject.message == "The device was not found"
    //         ) {
    //           deviceStatusResponse = "The device was not found or offline";
    //           SDKSuccessStatus = false;
    //         } else if (SdkResponseDeviceobject.message == "person info error") {
    //           let SDKUseridArray = SdkResponseDeviceobject.userList; //SDK error userslist
    //           jsrightEmployees.forEach((element) => {
    //             let systemUserid = element.system_user_id;
    //             SDKSuccessStatus = false;

    //             element["sdkEmpResponse"] = "Success";
    //             let selectedEmpobject = SDKUseridArray.find(
    //               (e) => e.userCode == systemUserid
    //             );
    //             EmpStatusResponse = SdkResponseDeviceobject.sdkEmpResponse;
    //             deviceStatusResponse = "";

    //             if (EmpStatusResponse != "") {
    //               //Adding extra parameters for Employee object
    //               if (selectedEmpobject) {
    //                 element["sdkEmpResponse"] = "person photo error ";
    //                 // $.extend(element, {
    //                 //   sdkEmpResponse: "person info error ",
    //                 // });
    //               } else {
    //                 // $.extend(element, {
    //                 //   sdkEmpResponse: " Success",
    //                 // });
    //                 element["sdkEmpResponse"] = " Success";
    //               }
    //             }
    //           });
    //         } else {
    //         }

    //         //Adding extra parameters for Devices object
    //         // $.extend(elementDevice, {
    //         //   sdkDeviceResponse:
    //         //     deviceStatusResponse != "" ? deviceStatusResponse : " Success",
    //         // });

    //         elementDevice["sdkDeviceResponse"] =
    //           deviceStatusResponse != "" ? deviceStatusResponse : "Success";
    //         this.errors = [];

    //         this.loading = false;
    //       });

    //       // data.data.forEach((e) => {
    //       //   const index = this.devices_dialog.findIndex(
    //       //     (item) => item.device_id === e.sn
    //       //   );
    //       //   if (index !== -1) {
    //       //     const updatedElement = {
    //       //       ...this.devices_dialog[index],
    //       //       state: e.state,
    //       //       message: e.message || "Success",
    //       //     };

    //       //     this.devices_dialog.splice(index, 1, updatedElement);
    //       //   }
    //       // });
    //     } else {
    //       this.loading_dialog = false;
    //       this.snackbar.show = true;
    //       this.response = data.message;

    //       this.loading = false;
    //     }

    //     this.displaybutton = true;
    //     // } catch (error) {
    //     //   this.loading_dialog = false;
    //     //   this.snackbar = true;
    //     //   this.response = error.message;

    //     // }
    //   });
    // },
    async onSubmit() {
      this.displaybutton = false;
      this.loading = true;
      if (this.rightEmployees.length == 0) {
        this.response = this.response + " Atleast select one Employee Details";
      } else if (this.rightDevices.length == 0) {
        this.response = this.response + " Atleast select one Device Details";
      }

      this.loading_dialog = true;
      this.errors = [];

      let personListArray = [];

      this.rightEmployees.forEach((item) => {
        let person = {
          name: item.first_name + " " + item.last_name,

          userCode: parseInt(item.system_user_id),
          profile_picture_raw: item.profile_picture_raw,
          //faceImage: `https://stagingbackend.ideahrms.com/media/employee/profile_picture/1686381362.jpg?t=786794`,
          faceImage:
            process.env.APP_ENV != "local"
              ? item.profile_picture
              : "https://backend.mytime2cloud.com/media/employee/profile_picture/1706172456.jpg",
        };
        if (item.rfid_card_number != "") {
          person.cardData = item.rfid_card_number;
        }
        if (item.rfid_card_password != "") {
          person.password = item.rfid_card_password;
        }
        personListArray.push(person);
      });

      let payload = {
        personList: personListArray,
        snList: this.rightDevices.map((e) => e.device_id),
        branch_id: this.branch_id,
      };

      if (payload.snList && payload.snList.length === 0) {
        alert(`Atleast one device must be selected`);
        return false;
      }

      this.devices_dialog.forEach((e) => {
        e.state = "---";
        e.message = "---";
      });

      //try {
      const { data } = await this.$axios.post(
        `/Person/AddRange/Photos`,
        payload
      );

      if (data.deviceResponse.status == 200) {
        this.loading_dialog = false;

        this.snackbar.show = true;
        this.response = "Employee(s) Pictures  has been uploaded";

        let jsrightEmployees = this.rightEmployees;
        let SDKSuccessStatus = true;
        jsrightEmployees.forEach((element) => {
          element["sdkEmpResponse"] = "Success";
        });
        this.rightDevices.forEach((elementDevice) => {
          elementDevice["sdkDeviceResponse"] = "Success";
          this.errors = [];
          this.loading = false;
        });

        setTimeout(() => {
          location.reload();
        }, 1000);
        // data.data.forEach((e) => {
        //   const index = this.devices_dialog.findIndex(
        //     (item) => item.device_id === e.sn
        //   );
        //   if (index !== -1) {
        //     const updatedElement = {
        //       ...this.devices_dialog[index],
        //       state: e.state,
        //       message: e.message || "Success",
        //     };

        //     this.devices_dialog.splice(index, 1, updatedElement);
        //   }
        // });
      } else {
        this.loading_dialog = false;
        this.snackbar.show = true;
        this.response = data.message;

        this.loading = false;
      }

      this.displaybutton = true;
      // } catch (error) {
      //   this.loading_dialog = false;
      //   this.snackbar = true;
      //   this.response = error.message;

      // }
    },
  },
};
</script>
<style scoped>
.employee-pic {
  padding: 0px;
  position: relative;
  top: 0;
  transition: top ease 1s;

  margin-left: -3px;
  width: 25px;
  border: 1px solid #ddd;
}

@media (max-width: 500px) {
  .employeepage-seach-textfield {
    max-width: 68px;
  }

  .xs-padding-0 {
    padding-right: 0px !important;
  }

  .xs-margin-5 {
    margin-top: 5px !important;
  }

  .v-data-table > .v-data-table__wrapper .v-data-table__mobile-table-row {
    margin: 10px;
    border: 1px solid #ededed;
    display: block;
  }
}
</style>
