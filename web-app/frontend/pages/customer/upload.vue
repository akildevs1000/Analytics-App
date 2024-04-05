<template>
  <div style="width: 100% !important">
    <div class="text-center ma-2">
      <v-snackbar
        :color="`primary`"
        v-model="snackbar"
        small
        top="top"
        :timeout="3000"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <v-col v-if="isCompany" cols="3">
        <v-select
          hide-details
          @change="filterDataByBranch(branch_id)"
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
      <v-col cols="9" class="text-right">
        <v-btn class="primary" @click="goback"> Back </v-btn>
      </v-col>
      <v-col cols="5">
        <v-card class="photo-displaylist" style="height: 300px">
          <v-toolbar color=" " dense flat style="border: 1px solid #ddd">
            <span> Users </span>
          </v-toolbar>
          <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden">
            <div
              v-for="user in leftUsers"
              :id="user.id"
              :key="user.id"
              style="
                border-bottom: 1px solid #ddd;
                display: flex;
                align-items: center;
              "
              class="px-5"
            >
              <v-checkbox
                dense
                small
                v-model="leftSelectedEmp"
                :value="user.id"
                primary
                hide-details
              ></v-checkbox>
              <v-avatar size="40" class="mr-5">
                <v-img
                  :src="
                    user.profile_picture
                      ? user.profile_picture
                      : '/no-profile-image.jpg'
                  "
                >
                </v-img>
              </v-avatar>
              <div class="pt-2">
                <strong>{{ user.full_name }}</strong>
                <br /><small>{{ user.system_user_id }}</small>
              </div>
            </div>
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
            <span>Selected Users </span>
          </v-toolbar>
          <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden">
            <div
              v-for="user in rightUsers"
              :id="user.id"
              :key="user.id"
              style="
                border-bottom: 1px solid #ddd;
                display: flex;
                align-items: center;
              "
              class="px-5"
            >
              <v-checkbox
                dense
                small
                v-model="rightSelectedEmp"
                :value="user.id"
                primary
                hide-details
              ></v-checkbox>
              <v-avatar size="40" class="mr-5">
                <v-img
                  :src="
                    user.profile_picture
                      ? user.profile_picture
                      : '/no-profile-image.jpg'
                  "
                >
                </v-img>
              </v-avatar>
              <div class="pt-2">
                <strong>{{ user.full_name }}</strong>
                <br /><small>{{ user.system_user_id }}</small>
              </div>
            </div>
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
      <v-col cols="12" class="text-right">
        <span v-if="errors && errors.message" class="text-danger mt-2">{{
          errors.message
        }}</span>
        <span v-if="serverErrorResponse" class="text-danger mt-2">
          {{ serverErrorResponse }}
        </span>
      </v-col>
      <v-col cols="12" class="text-right">
        <v-btn
          :disabled="!displaybutton"
          :loading="loading"
          @click="onSubmit"
          type="button"
          class="primary"
        >
          Submit
        </v-btn>
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
      snackbar: false,
      errors: [],
      response: "",
      serverErrorResponse: null,
      color: "primary",
      endpointDevise: "device",
      leftSelectedEmp: [],
      departmentselected: [],
      departments: [],
      leftUsers: [],
      rightSelectedEmp: [],
      rightUsers: [],
      leftSelectedDevices: [],
      leftDevices: [],
      rightSelectedDevices: [],
      rightDevices: [],
      department_ids: ["---"],
      timezones: ["Timeszones are not available"],
      timezonesselected: [],
    };
  },
  mounted: function () {
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
    await this.filterDataByBranch(this.branch_id);
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    async filterDataByBranch(branch_id) {
      await this.getDevisesDataFromApi(branch_id);
      await this.getUsersDataFromApi(branch_id);
    },
    resetErrorMessages() {
      this.errors = [];
      this.response = "";
      this.leftUsers.forEach((element) => {
        element["sdkEmpResponse"] = "";
      });
      this.leftDevices.forEach((element) => {
        element["sdkDeviceResponse"] = "";
      });
    },
    goback() {
      this.$router.push("/customer");
    },
    async getDevisesDataFromApi(branch_id) {
      this.$axios
        .get(this.endpointDevise, {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
            sortBy: "status_id",
            branch_id: branch_id,
          },
        })
        .then(({ data }) => {
          this.leftDevices = data.data;
        });
    },
    async getUsersDataFromApi(branch_id) {
      this.$axios
        .get(`customer-list`, {
          params: {
            branch_id,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          console.log(data);
          this.leftUsers = data;
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
    verifySubmitButton() {
      if (this.rightUsers.length > 0 && this.rightDevices.length > 0) {
        this.displaybutton = true;
      } else {
        this.displaybutton = false;
      }
    },
    allmoveToLeftemp() {
      this.resetErrorMessages();
      this.leftUsers = this.leftUsers.concat(this.rightUsers);
      this.rightUsers = [];
      this.leftUsers = this.sortObject(this.leftUsers);

      this.verifySubmitButton();
    },
    allmoveToRightEmp() {
      this.resetErrorMessages();

      this.rightUsers = this.rightUsers.concat(
        this.leftUsers.filter((el) => el.profile_picture != null)
      );

      this.leftUsers = this.leftUsers.filter(
        (el) => el.profile_picture == null
      );

      this.rightUsers = this.sortObject(this.rightUsers);
      this.verifySubmitButton();
    },
    moveToLeftempOption2() {
      this.resetErrorMessages();

      if (!this.rightSelectedEmp.length) return;
      //for (let i = this.leftSelectedEmp.length; i > 0; i--) {
      let _rightSelectedEmp_length = this.rightSelectedEmp.length;
      for (let i = 0; i < _rightSelectedEmp_length; i++) {
        if (this.rightSelectedEmp) {
          let selectedindex = this.rightUsers.findIndex(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          let selectedobject = this.rightUsers.find(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          selectedobject.sdkEmpResponse = "";
          this.leftUsers.push(selectedobject);

          this.rightUsers.splice(selectedindex, 1);
        }
      }
      this.leftUsers = this.sortObject(this.leftUsers);
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
          let selectedindex = this.rightUsers.findIndex(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          let selectedobject = this.rightUsers.find(
            (e) => e.id == this.rightSelectedEmp[i]
          );

          selectedobject.sdkEmpResponse = "";
          this.leftUsers.push(selectedobject);

          this.rightUsers.splice(selectedindex, 1);
        }
      }
      this.leftUsers = this.sortObject(this.leftUsers);

      this.rightSelectedEmp.pop(id);
      this.verifySubmitButton();
    },
    check: function (id, e) {},
    selectLeftEmployee(id) {
      this.leftSelectedEmp.push(id);
    },

    moveToRightEmpOption2() {
      this.resetErrorMessages();

      if (this.leftSelectedEmp.length === 0) return; // No need for double-checking length

      this.leftSelectedEmp.forEach((selectedId) => {
        let selectedObject = this.leftUsers.find(
          (user) => user.id === selectedId
        );
        if (selectedObject) {
          this.rightUsers.push(selectedObject);
          this.leftUsers = this.leftUsers.filter(
            (user) => user.id !== selectedId
          );
        }
      });

      this.rightUsers = this.sortObject(this.rightUsers);
      this.leftSelectedEmp = []; // Clearing the array directly

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

      if (this.leftSelectedDevices.length === 0) return;

      this.leftSelectedDevices.forEach((selectedId) => {
        let selectedDevice = this.leftDevices.find(
          (device) => device.id === selectedId
        );
        if (selectedDevice) {
          selectedDevice.sdkDeviceResponse = ""; // Clearing the property
          this.rightDevices.push(selectedDevice);
          this.leftDevices = this.leftDevices.filter(
            (device) => device.id !== selectedId
          );
        }
      });

      this.rightDevices = this.sortObjectD(this.rightDevices);
      this.leftSelectedDevices = []; // Clearing the array directly

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
    async onSubmit() {
      // this.displaybutton = false;
      this.loading = true;
      this.serverErrorResponse = null;
      if (this.rightUsers.length == 0) {
        this.response = this.response + " Atleast select one Employee Details";
      } else if (this.rightDevices.length == 0) {
        this.response = this.response + " Atleast select one Device Details";
      }

      this.errors = [];

      let personListArray = [];

      this.rightUsers.forEach((item) => {
        let person = {
          name: item.first_name + " " + item.last_name,
          cardData: null,
          password: null,

          userCode: parseInt(item.system_user_id),
          profile_picture_raw: item.profile_picture_raw,
          // faceImage:
          //   process.env.APP_ENV != "local"
          //     ? item.profile_picture
          //     : "https://backend.mytime2cloud.com/media/employee/profile_picture/1706172456.jpg",

          faceImage:
            "https://backend.mytime2cloud.com/media/employee/profile_picture/1706346188.jpg",
        };
        personListArray.push(person);
      });

      let payload = {
        personList: personListArray,
        snList: this.rightDevices.map((e) => e.device_id),
        branch_id: this.branch_id,
        company_id: this.$auth.user.company_id,
      };

      if (payload.snList && payload.snList.length === 0) {
        alert(`Atleast one device must be selected`);
        return false;
      }

      this.devices_dialog.forEach((e) => {
        e.state = "---";
        e.message = "---";
      });

      try {
        const { data, status } = await this.$axios.post(
          `/Person/AddRange/Photos/V1`,
          payload
        );

        this.snackbar = true;
        this.response = "Customer(s) has been uploaded";

        let jsrightUsers = this.rightUsers;
        jsrightUsers.forEach((element) => {
          element["sdkEmpResponse"] = "Success";
        });
        this.rightDevices.forEach((elementDevice) => {
          elementDevice["sdkDeviceResponse"] = "Success";
          this.errors = [];
          this.loading = false;
        });

        setTimeout(() => {
          this.goback();
        }, 1000);

        this.displaybutton = true;
      } catch (error) {
        this.loading = false;

        if (error.response && error.response.data) {
          // If the error is from a server response, show the detailed message from the server
          this.serverErrorResponse =
            error.response.data.message || "Unknown error occurred.";
        } else if (error.message) {
          // If the error has a message, show that message
          this.serverErrorResponse = error.message;
        } else {
          // Fallback message for other types of errors
          this.serverErrorResponse = "An error occurred.";
        }
      }
    },
  },
};
</script>
<style scoped>
@import url("@/assets/photo_upload.css");
</style>
