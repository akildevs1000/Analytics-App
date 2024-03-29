<template>
  <div style="width: 100% !important">
    <v-progress-linear
      v-if="progressloading"
      :active="loading"
      :indeterminate="loading"
      absolute
      color="primary"
    ></v-progress-linear>
    <div class="text-center ma-2">
      <v-snackbar
        :color="snackbar.color"
        v-model="snackbar.show"
        small
        top="top"
        :timeout="3000"
      >
        {{ snackbar.message }}
      </v-snackbar>
    </div>
    <v-row>
      <v-col cols="4">
        <v-text-field
          :disabled="true"
          v-model="name"
          dense
          outlined
          :hide-details="!errors.name"
          :error-messages="errors && errors.name && errors.name[0]"
          placeholder="Zone Name"
          required
        ></v-text-field>
      </v-col>
      <v-col cols="4"> </v-col>

      <v-col cols="4">
        <div class="text-right">
          <v-btn small fab class="primary">
            <v-icon small @click="goback()" color="white"
              >mdi-arrow-left</v-icon
            >
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="5">
        <v-toolbar color="background" dense flat dark>
          <span>All Devices List</span>
          <v-spacer></v-spacer>
        </v-toolbar>
        <div>
          <v-card style="min-height: 500px" scrollable>
            <v-card-text
              class="timezone-displaylistview"
              v-for="(device, index) in leftDevices"
              :id="device.id"
              v-model="leftSelectedDevices"
              :key="index"
            >
              <div class="row">
                <v-col class="col-1" style="padding: 0px">
                  <v-checkbox
                    :disabled="true"
                    hideDetails
                    class="col-1 d-flex flex-column justify-center"
                    v-model="leftSelectedDevices"
                    :value="device.id"
                    primary
                    hide-details
                  ></v-checkbox>
                </v-col>
                <div class="col-sm" style="padding-top: 21px; color: #000000">
                  {{ device.name }} : {{ device.device_id }}

                  <span
                    style="color: green"
                    v-if="device.status.name == 'active'"
                  >
                    Online</span
                  >
                  <span style="color: red" v-else>Offline </span>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </div>
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
            :disabled="true"
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
            :disabled="true"
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
            :disabled="true"
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
            :disabled="true"
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
        <v-toolbar color="background" dense flat dark>
          <span>Selected Devices List</span>
        </v-toolbar>
        <div>
          <v-card style="min-height: 500px">
            <v-card-text
              class="error--text text-center"
              v-if="errors && errors.device_ids"
            >
              {{ errors.device_ids && errors.device_ids[0] }}
            </v-card-text>
            <v-card-text
              class="timezone-displaylistview"
              v-for="(device, index) in rightDevices"
              :id="device.id"
              v-model="rightSelectedDevices"
              :key="index"
            >
              <div class="row">
                <v-col class="col-1" style="padding: 0px"
                  ><v-checkbox
                    :disabled="true"
                    hideDetails
                    class="col-1 d-flex flex-column justify-center"
                    v-model="rightSelectedDevices"
                    :value="device.id"
                    primary
                    hide-details
                  ></v-checkbox>
                </v-col>
                <div class="col-sm" style="padding-top: 21px; color: #000000">
                  {{ device.name }} : {{ device.device_id }}
                  <span
                    style="color: green"
                    v-if="device.status.name == 'active'"
                  >
                    Online</span
                  >
                  <span style="color: red" v-else>Offline </span>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col class="text-right">
        <button
          :disabled="true"
          :loading="loading"
          @click="onSubmit"
          type="button"
          id="save"
          class="btn primary white--text v-size--default"
        >
          Submit
        </button>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      displaybutton: false,
      progressloading: false,
      searchInput: "",
      snackbar: {
        message: "",
        color: "background",
        show: true,
      },
      errors: [],
      response: "",
      color: "primary",
      loading: true,
      leftSelectedDevices: [],
      leftDevices: [],
      rightSelectedDevices: [],
      rightDevices: [],
      name: "",
      options: {
        params: {
          company_id: this.$auth.user.company_id,
          cols: ["id", "name"],
        },
      },
    };
  },
  mounted: function () {
    this.snackbar.show = true;
    this.snackbar.message = "Data loading...Please wait ";
    this.response = "Data loading...Please wait ";

    this.$nextTick(function () {
      setTimeout(() => {
        this.loading = false;
      }, 2000);
    });

    setTimeout(() => {
      this.loading = false;
    }, 2000);
  },
  created() {
    this.getDevisesDataFromApi();
    this.getTimezonesFromApi();
  },
  methods: {
    getZone() {
      this.$axios
        .get(`zone/${this.$route.params.id}`)
        .then(({ data }) => {
          this.name = data.name;
          this.leftSelectedDevices = data.devices.map((e) => e.id);
          this.moveToRightDevicesOption2();
        })
        .catch((err) => console.log(err));
    },
    getTimezonesFromApi() {
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios
        .get("timezone", options)
        .then(({ data }) => {
          this.timezones = data.data;
        })
        .catch((err) => console.log(err));
    },
    resetErrorMessages() {
      this.errors = [];
      Object.assign(this.snackbar, {});
    },

    onSubmit() {
      this.resetErrorMessages();

      let payload = {
        name: this.name,
        device_ids: this.rightDevices.map((e) => e.id),
        company_id: this.$auth.user.company_id,
      };

      this.$axios
        .put(`zone/${this.$route.params.id}`, payload)
        .then(({ data }) => {
          this.errors = [];

          this.snackbar = {
            message: data.message,
            color: "background",
            show: true,
          };

          // this.rightDevices.forEach((e, i) => {
          //   let found = data.find((re) => re.sn == e.device_id);

          //   if (found.state == true) {
          //     found["sdkDeviceResponse"] = "Success";
          //     return;
          //   } else {
          //     found["sdkDeviceResponse"] = "The device was not found or offline";
          //     SDKSuccessStatus = false;
          //   }
          // });
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }
        });
    },
    goback() {
      this.$router.push("/zone");
    },
    getDevisesDataFromApi() {
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          // cols: ["id", "location", "name", "device_id"],
        },
      };
      let page = 1;
      this.$axios.get(`device?page=${page}`, options).then(({ data }) => {
        this.leftDevices = data.data;
        this.getZone();
      });
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
    /* Devices---------------------------------------- */
    allmoveLeftDevices() {
      this.resetErrorMessages();
      this.leftDevices = this.rightDevices;
      this.rightDevices = [];

      this.leftDevices = this.sortObjectD(this.leftDevices);
    },
    allmoveRightDevices() {
      this.resetErrorMessages();
      //this.rightDevices = this.rightDevices.concat(this.leftDevices);
      //this.leftDevices = [];
      this.rightDevices = this.leftDevices;
      this.leftDevices = [];
      this.rightDevices = this.sortObjectD(this.rightDevices);
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
    },
  },
};
</script>
