<template>
  <div style="width: 100% !important">
    <div>
      <!-- <v-row>
        <v-col cols="12">
          <v-row class="mt-5  ">
            <v-col cols="4">
              <h4>Timezone Mapping - new</h4>

            </v-col>
          </v-row>
          <v-progress-linear v-if="progressloading" :active="loading" :indeterminate="loading" absolute
            color="primary"></v-progress-linear>
        </v-col>
      </v-row> -->
      <v-row>
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
      </v-row>
      <v-row>
        <v-col v-if="isCompany" cols="3">
          <v-select
            @change="filterDepartmentsByBranch($event)"
            v-model="branch_id"
            :items="[{ id: ``, branch_name: `Select All` }, ...branchesList]"
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
        <v-col cols="3">
          <v-select
            v-model="timezonesselected"
            :items="timezones"
            dense
            outlined
            item-value="timezone_id"
            item-text="timezone_name"
            hide-details
            label="Timezones"
            required
          ></v-select>
        </v-col>

        <!-- <v-col cols="2" class="toolbaritems-button-design text-right">

          <v-btn @click="goback()" style="width:130px" align="right" small dark class="primary "><v-icon color="white">mdi
              mdi-format-list-bulleted-square</v-icon>
            View List
          </v-btn>
        </v-col> -->

        <v-col cols="3">
          <div class="text-right">
            <v-btn
              small
              color="primary"
              style="width: 130px"
              @click="goback()"
              class="mb-2"
              ><v-icon color="white"
                >mdi mdi-format-list-bulleted-square</v-icon
              >
              View List</v-btn
            >
          </div>
        </v-col>
        <!-- <div>
          <button @click="goback()" type="button" id="back" class="btn primary btn-block white--text v-size--default">
            <v-icon color="white">mdi mdi-format-list-bulleted-square</v-icon>
            View List
          </button>
        </div> -->
      </v-row>
      <v-row>
        <v-col cols="5">
          <v-card class="timezone-displaylist1" style="height: 300px">
            <v-toolbar dense flat style="border-bottom: 1px solid #ddd">
              <span> Employees </span>
            </v-toolbar>
            <div style="height: 245px; overflow-y: auto; overflow-x: hidden">
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
                      v-if="
                        user.timezone.timezone_name == '---' ||
                        user.timezone.timezone_id == 1
                      "
                      dense
                      small
                      hideDetails
                      v-model="leftSelectedEmp"
                      :value="user.id"
                      primary
                      hide-details
                    ></v-checkbox>
                    <v-checkbox
                      style="padding: 0px"
                      v-else
                      dense
                      small
                      hideDetails
                      v-model="leftSelectedEmp"
                      :value="user.id"
                      primary
                      hide-details
                    ></v-checkbox>
                  </v-col>

                  <v-col style="padding: 0px">
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
                  <v-col md="3" style="padding: 0px">
                    <span
                      v-if="user.timezone && user.timezone.timezone_id != 1"
                    >
                      {{
                        user.timezone.timezone_name == "---"
                          ? "---"
                          : user.timezone.timezone_name + " Assigned"
                      }}
                    </span>
                  </v-col>
                </v-row>
              </v-card-text>
              <!-- <v-card-text
                class="timezone-displaylistview1"
                v-for="(user, index) in leftEmployees"
                :id="user.id"
                v-model="leftSelectedEmp"
                :key="user.id"
              >
                <div class="row">
                  <v-col class="col-1" style="padding: 0px">
                    <v-checkbox
                      v-if="
                        user.timezone.timezone_name == '---' ||
                        user.timezone.timezone_id == 1
                      "
                      hideDetails
                      class="col-1 d-flex flex-column justify-center"
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
                  <div
                    class="col-8"
                    :style="{
                      color:
                        user.timezone && user.timezone.timezone_name
                          ? '#000000'
                          : '#b4b0b0',
                    }"
                    style="padding-top: 21px"
                  >
                    {{ user.employee_id }}: {{ user.first_name }}
                    {{ user.last_name }}:
                    <span
                      v-if="user.timezone && user.timezone.timezone_id != 1"
                    >
                      {{
                        user.timezone.timezone_name == "---"
                          ? "---"
                          : user.timezone.timezone_name + " Assigned"
                      }}
                    </span>
                  </div>
                  <div class="col-sm"></div>
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
          <v-card class="timezone-displaylist1" style="height: 300px">
            <v-toolbar
              color=" "
              dense
              flat
              style="border-bottom: 1px solid #ddd"
            >
              <span>Selected Employees List</span>
            </v-toolbar>
            <div
              style="max-height: 245px; overflow-y: auto; overflow-x: hidden"
            >
              <v-card-text>
                <v-row
                  class="timezone-displaylistview1"
                  v-for="(user, index) in rightEmployees"
                  :id="user.id"
                  v-model="leftSelectedEmp"
                  :key="user.id"
                  style="border-bottom: 1px solid #ddd"
                >
                  <v-col md="1" style="padding: 0px;margin-top-3">
                    <v-checkbox
                      v-if="
                        user.timezone.timezone_name == '---' ||
                        user.timezone.timezone_id == 1
                      "
                      dense
                      small
                      hideDetails
                      v-model="rightSelectedEmp"
                      :value="user.id"
                      primary
                      hide-details
                    ></v-checkbox>
                    <v-checkbox
                      style="padding: 0px;margin-top-3"
                      v-else
                      dense
                      small
                      hideDetails
                      v-model="rightSelectedEmp"
                      :value="user.id"
                      primary
                      hide-details
                    ></v-checkbox>
                  </v-col>
                  <v-col md="3" style="padding: 0px; padding-top: 5px">
                    {{ user.first_name }}
                    {{ user.last_name }}
                  </v-col>
                  <v-col md="3" style="padding: 0px; padding-top: 5px">
                    {{ user.employee_id }}
                  </v-col>
                  <v-col md="3" style="padding: 0px">
                    <span style="color: red">{{ user.sdkEmpResponse }}</span>
                  </v-col>
                </v-row>
              </v-card-text>

              <!-- 
              <v-card-text
                class="timezone-displaylistview"
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
                  <div class="col-sm" style="padding-top: 21px; color: #000000">
                    {{ user.employee_id }} : {{ user.first_name }}
                    {{ user.last_name }}
                  </div>
                  <div class="col-sm" style="padding-top: 21px">
                    <span style="color: red">{{ user.sdkEmpResponse }}</span>
                  </div>
                </div>
              </v-card-text> -->
            </div>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="5">
          <v-card class="timezone-displaylist1" style="height: 305px">
            <v-toolbar
              color=" "
              dense
              flat
              style="border-bottom: 1px solid #ddd"
            >
              <span> Devices </span>
            </v-toolbar>
            <div
              style="max-height: 260px; overflow-y: auto; overflow-x: hidden"
            >
              <!-- <v-card-text>
                <table class="basic-table-design">
                  <tr
                    v-for="(user, index) in leftDevices"
                    :id="user.id"
                    :key="user.id"
                  >
                    <td style="width: 50px">
                      <v-checkbox
                        dense
                        small
                        hideDetails
                        v-model="leftSelectedDevices"
                        :value="user.id"
                        primary
                        hide-details
                      ></v-checkbox>
                    </td>
                    <td style="width: 25%">{{ user.name }}</td>
                    <td>{{ user.model_number }}</td>
                    <td>
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
                    </td>
                  </tr>
                </table>
              </v-card-text> -->
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
              @click="allmoveRightDevices"
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
              @click="allmoveLeftDevices"
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
          <v-card class="timezone-displaylist1" style="height: 305px">
            <v-toolbar
              color=" "
              dense
              flat
              style="border-bottom: 1px solid #ddd"
            >
              <span>Selected Devices List</span>
            </v-toolbar>
            <div
              style="max-height: 260px; overflow-y: auto; overflow-x: hidden"
            >
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
                      dense
                      small
                      hideDetails
                      v-model="rightSelectedDevices"
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
                      style="width: 30px" />
                    <img
                      title="Offline"
                      v-else
                      src="/icons/device_status_close.png"
                      style="width: 30px"
                  /></v-col>
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
                class="timezone-displaylistview"
                v-for="(user, index) in rightDevices"
                :id="user.id"
                v-model="rightSelectedDevices"
                :key="user.id"
              >
                <div class="row">
                  <v-col class="col-1" style="padding: 0px"
                    ><v-checkbox
                      hideDetails
                      class="col-1 d-flex flex-column justify-center"
                      v-model="rightSelectedDevices"
                      :value="user.id"
                      primary
                      hide-details
                    ></v-checkbox>
                  </v-col>
                  <div class="col-sm" style="padding-top: 21px; color: #000000">
                    {{ user.name }} : {{ user.model_number }}
                  </div>
                  <div class="col-sm" style="padding-top: 21px">
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
        <v-col cols="12">
          <div class="row">
            <div class="col col-lg-6 text-center">
              <span v-if="errors && errors.message" class="text-danger mt-2">{{
                errors.message
              }}</span>
            </div>
            <div class="col col-lg-3 text-right">
              <div style="width: 150px; float: right">
                <!-- <button
                  :loading="loading"
                  @click="goback()"
                  type="button"
                  id="save"
                  class="btn primary btn-block white--text v-size--default"
                >
                  Back
                </button> -->
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
    <!-- <Preloader v-else /> -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      branch_id: null,
      branchesList: [],
      displaybutton: false,
      progressloading: false,
      searchInput: "",
      snackbar: {
        message: "",
        color: "black",
        show: true,
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
      isCompany: true,
    };
  },
  mounted: function () {
    this.snackbar.show = true;
    this.snackbar.message = "Data loading...Please wait ";
    this.response = "Data loading...Please wait ";

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
    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      this.filterDepartmentsByBranch(this.branch_id);
      return;
    }

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
    filterDepartmentsByBranch(branch_id) {
      this.getDepartmentsApi(this.options, branch_id);
      this.getDevisesDataFromApi(branch_id);
      this.getEmployeesDataFromApi(branch_id);
      this.getTimezonesFromApi(branch_id);
    },
    getDepartmentsApi(options, branch_id) {
      options.params.branch_id = branch_id;
      console.log(options);
      this.$axios
        .get("departments", options)
        .then(({ data }) => {
          this.departments = data.data;
          this.departments.unshift({ id: "---", name: "All Departments" });
        })
        .catch((err) => console.log(err));
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

    verifySubmitButton() {
      if (this.rightEmployees.length > 0 && this.rightDevices.length > 0) {
        this.displaybutton = true;
      } else {
        this.displaybutton = false;
      }
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
          cols: [
            "id",
            "employee_id",
            "display_name",
            "first_name",
            "last_name",
          ],
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
    resetErrorMessages() {
      this.errors = [];
      this.response = "";

      // $.extend(this.leftEmployees, {
      //   sdkEmpResponse: "",
      // });
      // $.extend(this.leftDevices, {
      //   sdkDeviceResponse: "",
      // });
      this.leftEmployees.forEach((element) => {
        element["sdkEmpResponse"] = "";
      });
      this.leftDevices.forEach((element) => {
        element["sdkDeviceResponse"] = "";
      });
    },
    onSubmit() {
      this.resetErrorMessages();
      this.displaybutton = false;
      if (this.timezonesselected == "") {
        this.response = this.response + "Timezones not selected";
      } else if (this.rightEmployees.length == 0) {
        this.response = this.response + " Atleast select one Employee Details";
      } else if (this.rightDevices.length == 0) {
        this.response = this.response + " Atleast select one Device Details";
      }

      if (this.response != "") {
        this.snackbar.show = true;
        this.snackbar.message = this.response;
        this.snackbar.color = "red";
        setTimeout(() => {
          this.snackbar.show = false;
        }, 1000 * 10);
        return false;
      }
      this.loading = true;

      let columnsToFilter = ["systeM_user_id"];
      let onlyUserSystemids = {};

      // Define the keys you want to select
      let keysToSelect = ["system_user_id"];

      // Select the specified keys from each object
      let filteredDataEmp = [];
      this.rightEmployees.map(function (obj) {
        let selectedObj = {};
        keysToSelect.forEach(function (key) {
          if (obj.hasOwnProperty(key)) {
            // selectedObj[key] = obj[key];
            selectedObj = obj[key];
            filteredDataEmp.push(selectedObj);
          }
        });
        return selectedObj;
      });
      //
      // Define the keys you want to select
      keysToSelect = ["device_id"];

      // Select the specified keys from each object
      let filteredDataDevices = [];
      this.rightDevices.map(function (obj) {
        let selectedObj = {};
        keysToSelect.forEach(function (key) {
          if (obj.hasOwnProperty(key)) {
            // selectedObj[key] = obj[key];
            selectedObj = obj[key];
            filteredDataDevices.push(selectedObj);
          }
        });
        return selectedObj;
      });
      let options = {
        timezone_id: this.timezonesselected,
        employee_id: this.rightEmployees,
        device_id: this.rightDevices,
        company_id: this.$auth.user.company_id,
        employee_ids: filteredDataEmp,
        device_ids: filteredDataDevices,
        branch_id: this.branch_id,
      };

      this.progressloading = true;
      let jsrightEmployees = this.rightEmployees;

      let SDKSuccessStatus = true;
      this.$axios
        .post(`${this.endpointUpdatetimezoneStore}`, options)
        .then(({ data }) => {
          if (data.status) {
            // this.snackbar.show = true;
            // this.snackbar.message = "Employee(s) has been mapped";
            this.$router.push("/timezonemapping/list");
          }
          if (data.record.SDKResponse) {
            this.loading = false;
            this.rightDevices.forEach((rightDevicesobj) => {
              let SdkResponseDeviceobject = data.record.SDKResponse.data.find(
                (e) => e.sn == rightDevicesobj.device_id
              );

              let deviceStatusResponse = "";
              let EmpStatusResponse = "";

              if (SdkResponseDeviceobject.message == "") {
                deviceStatusResponse = "Success";
              } else if (
                SdkResponseDeviceobject.message == "The device was not found"
              ) {
                deviceStatusResponse = "The device was not found or offline";
                SDKSuccessStatus = false;
              } else if (
                SdkResponseDeviceobject.message == "person info error"
              ) {
                let SDKUseridArray = SdkResponseDeviceobject.userList; //SDK error userslist
                jsrightEmployees.forEach((element) => {
                  element["sdkEmpResponse"] = "Success";
                  let systemUserid = element.system_user_id;
                  SDKSuccessStatus = false;
                  let selectedEmpobject = SDKUseridArray.find(
                    (e) => e.userCode == systemUserid
                  );
                  EmpStatusResponse = SdkResponseDeviceobject.sdkEmpResponse;
                  deviceStatusResponse = "";

                  if (EmpStatusResponse != "") {
                    //Adding extra parameters for Employee object
                    if (selectedEmpobject) {
                      element["sdkEmpResponse"] = "person photo error ";
                      // $.extend(element, {
                      //   sdkEmpResponse: "person info error ",
                      // });
                    } else {
                      // $.extend(element, {
                      //   sdkEmpResponse: " Success",
                      // });
                      element["sdkEmpResponse"] = "Success";
                    }
                  }
                });
              } else {
              }

              //Adding extra parameters for Devices object
              rightDevicesobj["sdkDeviceResponse"] =
                deviceStatusResponse != "" ? deviceStatusResponse : " Success";
              this.errors = [];
            });
            // $.each(this.rightDevices, function (index, rightDevicesobj) {
            //   let SdkResponseDeviceobject = data.record.SDKResponse.data.find(
            //     (e) => e.sn == rightDevicesobj.device_id
            //   );

            //   let deviceStatusResponse = "";
            //   let EmpStatusResponse = "";

            //   if (SdkResponseDeviceobject.message == "") {
            //     deviceStatusResponse = "Success";
            //   } else if (
            //     SdkResponseDeviceobject.message == "The device was not found"
            //   ) {
            //     deviceStatusResponse = "The device was not found or offline";
            //     SDKSuccessStatus = false;
            //   } else if (
            //     SdkResponseDeviceobject.message == "person info error"
            //   ) {
            //     let SDKUseridArray = SdkResponseDeviceobject.userList; //SDK error userslist
            //     jsrightEmployees.forEach((element) => {
            //       element["sdkEmpResponse"] = "Success";
            //       let systemUserid = element.system_user_id;
            //       SDKSuccessStatus = false;
            //       let selectedEmpobject = SDKUseridArray.find(
            //         (e) => e.userCode == systemUserid
            //       );
            //       EmpStatusResponse = SdkResponseDeviceobject.sdkEmpResponse;
            //       deviceStatusResponse = "";

            //       if (EmpStatusResponse != "") {
            //         //Adding extra parameters for Employee object
            //         if (selectedEmpobject) {
            //           element["sdkEmpResponse"] = "person photo error ";
            //           // $.extend(element, {
            //           //   sdkEmpResponse: "person info error ",
            //           // });
            //         } else {
            //           // $.extend(element, {
            //           //   sdkEmpResponse: " Success",
            //           // });
            //           element["sdkEmpResponse"] = "Success";
            //         }
            //       }
            //     });
            //   } else {
            //   }

            //   //Adding extra parameters for Devices object
            //   rightDevicesobj["sdkDeviceResponse"] =
            //     deviceStatusResponse != "" ? deviceStatusResponse : " Success";
            //   this.errors = [];
            // });
            this.rightEmployees = jsrightEmployees;
            this.progressloading = false;

            this.loading = false;
            if (!SDKSuccessStatus) {
              {
                this.errors = data.errors;
              }
              this.errors = [];
              this.errors["message"] =
                "Device/Employee Error:   Device and Employee details are Mapped. You can add/remove items from Edit list ";

              //this.displaybutton = false;
            } else {
              this.$router.push("/timezonemapping/list");
            }
          } else {
            this.errors = [];
            this.progressloading = false;
            this.snackbar.show = true;
            this.errors["message"] =
              data.message +
              " But, Error:" +
              "Device Communication is not available";

            this.snackbar.message =
              data.message +
              "But,  Error:" +
              "Device Communication is not available";
            this.response =
              data.message +
              "But,  Error:" +
              "Device Communication is not available";
            return false;
            this.errors = [];
            this.progressloading = false;

            this.errors["message"] = "Device Communication is not available";

            this.snackbar.show = true;
            this.snackbar.message = "Device Communication is not available ";
            this.response = "Device Communication is not available ";
            return false;
          }

          this.displaybutton = true;
        });
    },
    goback() {
      this.$router.push("/timezonemapping/list");
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
        this.leftEmployees.filter(
          (el) =>
            el.timezone.timezone_name == "---" || el.timezone.timezone_id == 1
        )
      );

      this.leftEmployees = this.leftEmployees.filter(
        (el) =>
          el.timezone.timezone_name != "---" && el.timezone.timezone_id != 1
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

    moveToRightEmpOption2() {
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
    allmoveLeftDevices() {
      this.resetErrorMessages();
      this.leftDevices = this.leftDevices.concat(this.rightDevices);
      this.rightDevices = [];

      this.leftDevices = this.sortObjectD(this.leftDevices);
      this.verifySubmitButton();
    },
    allmoveRightDevices() {
      this.resetErrorMessages();
      //this.rightDevices = this.rightDevices.concat(this.leftDevices);
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
  },
};
</script>
<style scoped>
/* table {
  font-family: Roboto !important;
}

.fixed-setting {
  position: fixed !important;
  top: 500px;
  z-index: 100000;
  transition: right 1000ms !important;
  right: -15px !important;
}



.setting-drawer-open {
  right: 250px !important;
}

.setting-drawer-close {
  right: -15px !important;
}

.spin {
  -webkit-animation: spin 4s linear infinite;
  -moz-animation: spin 4s linear infinite;
  animation: spin 4s linear infinite;

  margin: 0 12px 0 0px !important;
  padding: 0 !important;
}

@-moz-keyframes spin {
  100% {
    -moz-transform: rotate(360deg);
  }
}

@-webkit-keyframes spin {
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

.stg-color-icon {
  width: 30px !important;
  height: 30px !important;
}

@media (min-width: 1264px) {
  .container {
    max-width: 100%;
  }
}

.submenutitle {
  padding-left: 5px;
  margin-left: -15px;
}

table.employee-table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {

  text-align: left;
  padding: 8px;
}

table.employee-table tr:nth-child(even) {
  background-color: #e9e9e9;
}

table.employee-table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
   
  text-align: left;
  padding: 8px;
}

table.employee-table tr:nth-child(even) {
  background-color: #e9e9e9;
}

.toolbaritems-button-design {
  padding-top: 8px !important;
  padding-right: 0px !important;

}

.toolbaritems-button-design .v-btn {
  height: 32px !important;
}

.timezone-displaylist {
  height: 225px !important;
  background: #fff;
  border-bottom-left-radius: 6px !important;
  border-bottom-right-radius: 6px !important;
  overflow: auto;
}

.timezone-displaylistview {
  padding-left: 10px !important;
  padding-bottom: 5px !important;
  padding-top: 0px !important;
  cursor: pointer !important;

  border-bottom: 1px solid #ddd;
}

.photo-displaylist {
  height: 225px !important;
  background: #fff !important;
  border-bottom-left-radius: 6px !important;
  border-bottom-right-radius: 6px !important;
  overflow: auto;
}

.photo-displaylistview {
  padding-left: 10px !important;
  padding-bottom: 5px !important;
  padding-top: 0px !important;
  cursor: pointer;

  border-bottom: 1px solid #ddd;
}

.timezoneedit-displaylist {
  height: 225px !important;
  background: #fff;
  border-bottom-left-radius: 6px !important;
  border-bottom-right-radius: 6px !important;
  overflow: auto;
}

.timezoneedit-displaylistview {
  padding-left: 10px !important;
  padding-bottom: 5px !important;
  padding-top: 0px !important;

  border-bottom: 1px solid #ddd;
}

.v-small-dialog__menu-content {
  margin-left: 3%;
  margin-top: 3%;
}

.employeepage-seach-textfield:focus {
  border: 0px !important;
  box-shadow: none !important;
}

.sortable {
  font-weight: bold;
  color: black !important;
}

.v-data-table-header .desc i {
  color: red !important;
}

.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}

.container {
  max-width: 100% !important;
}

.text-overflow {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.text-overflow-parent {
  max-width: 100px;
}

.text-overflow-child {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 100px;
}

.vertical-center {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 10px;
  width: 100%;
}

.center-child {
  position: absolute;
  top: 50%;
  left: 30%;
  margin: -50px 0 0 -50px;
}

tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}

.v-picker--date {
  height: 420px !important;
}

.table-search-header {
  padding: 0px;
  font-size: 10px;
  padding-top: 10px;
}

.table-search-header .theme--light.v-input input {
  display: inline;
  padding-top: 0px;
}

.table-search-header .v-text-field--outlined fieldset,
.table-search-header .v-text-field--outlined .v-text-field__slot {
  height: 32px !important;
  width: 100% !important;
}

.table-search-header .v-input__icon--clear {
  margin-right: 0px !important;
}

.table-search-header
  .v-text-field--outlined
  > .v-input__control
  > .v-input__slot {
  min-height: 32px !important;
}

.filter-select-hidden-text .v-input__append-inner {
  width: 100%important;
}

.table-search-header td {
  border: 0px !important;
  padding-top: 10px;
}

.table-search-header .v-text-field__details {
  display: none !important;
}

.table-search-header .v-input__icon--clear {
  margin-top: -17px;
  margin-right: 31px;
}

.filter-select-hidden-text input {
  display: none !important;
}

.filter-select-hidden-text .v-input__append-inner {
  margin: 0px !important;
  margin-left: -54px !important;
}

.filter-select-hidden-text .mdi-close {
  font-size: 8px !important;
}

.filter-select-hidden-text .v-select__selection {
  font-size: 13px !important;
}

.announcement-dropdown .v-select__selections {
  height: 33px;
  overflow: hidden;
}

.dialog-close-button {
  margin-top: -35px px;

  margin-right: -26px;
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
} */

.employee-pic {
  padding: 0px;
  position: relative;
  top: 0;
  transition: top ease 1s;

  margin-left: -3px;
  width: 25px;
  border: 1px solid #ddd;
}
</style>
