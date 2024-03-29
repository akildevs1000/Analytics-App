<template>
  <v-dialog persistent v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <span style="cursor: pointer" v-bind="attrs" v-on="on">
        <v-btn dense small class="primary" text title="Add Company">
          Create Tanent
          <v-icon right dark>mdi-plus-circle-outline</v-icon>
        </v-btn>
      </span>
    </template>
    <v-stepper v-model="step" horizontal>
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1" editable>
          Basic Info
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 2" step="2" editable>
          Vehicle Info
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 3" step="3" editable>
          Documentation
        </v-stepper-step>
      </v-stepper-header>

      <v-stepper-content step="1">
        <!-- Step 1 Content -->
        <v-row>
          <v-col cols="3">
            <div class="text-center">
              <SnippetsUploadAttachment
                :defaultImage="setImagePreview"
                @uploaded="handleAttachment"
              />

              <span v-if="errors && errors.logo" class="text-danger mt-2">{{
                errors.logo[0]
              }}</span>
            </div>
          </v-col>
          <v-col cols="9">
            <v-row class="mt-4">
              <v-col cols="6">
                <v-autocomplete
                  @change="getRoomsByFloorId(payload.floor_id)"
                  label="Floor Number"
                  outlined
                  :readonly="disabled"
                  v-model="payload.floor_id"
                  :items="floors"
                  dense
                  item-text="floor_number"
                  item-value="id"
                  :hide-details="!errors.floor_id"
                  :error-messages="
                    errors && errors.floor_id ? errors.floor_id[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-autocomplete
                  @change="getRoomNumber(payload.room_id)"
                  label="Room"
                  outlined
                  :readonly="disabled"
                  v-model="payload.room_id"
                  :items="rooms"
                  dense
                  item-text="room_number"
                  item-value="id"
                  :hide-details="!errors.room_id"
                  :error-messages="
                    errors && errors.room_id ? errors.room_id[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="First Name"
                  :readonly="disabled"
                  v-model="payload.first_name"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.first_name"
                  :error-messages="
                    errors && errors.first_name ? errors.first_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="Last Name"
                  :readonly="disabled"
                  v-model="payload.last_name"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.last_name"
                  :error-messages="
                    errors && errors.last_name ? errors.last_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-radio-group
                  class="ma-0 px-2 pa-0"
                  v-model="payload.gender"
                  row
                  :hide-details="!errors.gender"
                  :error-messages="
                    errors && errors.gender ? errors.gender[0] : ''
                  "
                >
                  <v-radio label="Male" value="Male"></v-radio>
                  <v-radio label="Female" value="Female"></v-radio>
                  <v-radio label="Other" value="Other"></v-radio>
                </v-radio-group>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="Email"
                  :readonly="disabled"
                  v-model="payload.email"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-menu
                  v-model="menu3"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="payload.date_of_birth"
                      label="Date of Birth"
                      append-icon="mdi-calendar"
                      outlined
                      dense
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      :hide-details="!errors.date_of_birth"
                      :error-messages="
                        errors && errors.date_of_birth
                          ? errors.date_of_birth[0]
                          : ''
                      "
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.date_of_birth"
                    @input="menu3 = false"
                    no-title
                    scrollable
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="Phone Number"
                  :readonly="disabled"
                  v-model="payload.phone_number"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.phone_number"
                  :error-messages="
                    errors && errors.phone_number ? errors.phone_number[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="Whatsapp Number (optional)"
                  :readonly="disabled"
                  v-model="payload.whatsapp_number"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.whatsapp_number"
                  :error-messages="
                    errors && errors.whatsapp_number
                      ? errors.whatsapp_number[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="RFID"
                  :readonly="disabled"
                  v-model="payload.rfid"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.rfid"
                  :error-messages="errors && errors.rfid ? errors.rfid[0] : ''"
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="PIN"
                  :readonly="disabled"
                  v-model="payload.pin"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.pin"
                  :error-messages="errors && errors.pin ? errors.pin[0] : ''"
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="Nationality"
                  :readonly="disabled"
                  v-model="payload.nationality"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.nationality"
                  :error-messages="
                    errors && errors.nationality ? errors.nationality[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="Address"
                  :readonly="disabled"
                  v-model="payload.address"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.address"
                  :error-messages="
                    errors && errors.address ? errors.address[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="payload.start_date"
                      label="Start Date"
                      append-icon="mdi-calendar"
                      outlined
                      dense
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      :hide-details="!errors.start_date"
                      :error-messages="
                        errors && errors.start_date ? errors.start_date[0] : ''
                      "
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.start_date"
                    @input="menu = false"
                    no-title
                    scrollable
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6">
                <v-menu
                  v-model="menu2"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="payload.end_date"
                      label="End Date"
                      append-icon="mdi-calendar"
                      outlined
                      dense
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      :hide-details="!errors.end_date"
                      :error-messages="
                        errors && errors.end_date ? errors.end_date[0] : ''
                      "
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.end_date"
                    @input="menu2 = false"
                    no-title
                    scrollable
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col cols="12">
                <v-switch
                  style="margin-top: 5px"
                  label="Web Access"
                  outlined
                  :readonly="disabled"
                  v-model="payload.web_access"
                  dense
                  :hide-details="!errors.web_access"
                  :error-messages="
                    errors && errors.web_access ? errors.web_access[0] : ''
                  "
                >
                </v-switch>
              </v-col>

              <v-col cols="6" class="my-1">
                <v-btn
                  v-if="formAction == 'Edit'"
                  class="primary"
                  @click="update_data"
                  >Update</v-btn
                >
              </v-col>

              <v-col cols="6" class="text-right my-1">
                <v-btn @click="dialog = false">Close</v-btn>
                <v-btn
                  v-if="formAction == 'Create'"
                  class="primary"
                  @click="tanentValidate"
                  >Next</v-btn
                >
                <v-btn
                  v-if="formAction == 'Edit'"
                  class="primary"
                  @click="tanentUpdateValidate"
                  >Next</v-btn
                >
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-stepper-content>

      <v-stepper-content step="2">
        <v-container>
          <v-row no-gutters>
            <v-col cols="2">
              <v-icon color="primary" @click="addItem"
                >mdi-plus-circle-outline</v-icon
              >
            </v-col>
          </v-row>
          <v-row
            class="mt-0"
            v-for="(vehicles, index) in vehicles"
            :key="index"
          >
            <v-col cols="5">
              <v-text-field
                label="Car Number"
                :readonly="disabled"
                v-model="vehicles.car_number"
                dense
                class="text-center"
                outlined
                :hide-details="!errors.car_number"
                :error-messages="
                  errors && errors.car_number ? errors.car_number[0] : ''
                "
              ></v-text-field>
            </v-col>
            <v-col cols="5">
              <v-autocomplete
                label="Parking Number"
                outlined
                :readonly="disabled"
                v-model="vehicles.parking_id"
                :items="[
                  {
                    id: 1,
                    parking_number: `P1`,
                  },
                  {
                    id: 2,
                    parking_number: `P2`,
                  },
                ]"
                dense
                item-text="parking_number"
                item-value="id"
                :hide-details="!errors.parking_number"
                :error-messages="
                  errors && errors.parking_number
                    ? errors.parking_number[0]
                    : ''
                "
              >
              </v-autocomplete>
            </v-col>

            <v-col cols="2">
              <v-icon color="red" @click="deleteItem(index)"
                >mdi-close-circle-outline</v-icon
              >
            </v-col>
          </v-row>
        </v-container>
        <v-row>
          <v-col class="text-right my-1">
            <v-btn @click="dialog = false">close</v-btn>
            <v-btn class="primary" @click="vehicleValidate">Next</v-btn>
          </v-col>
        </v-row>
      </v-stepper-content>

      <v-stepper-content step="3">
        <!-- Step 2 Content -->
        <v-row>
          <v-col v-for="(document, index) in documents" :key="index" cols="6">
            <SnippetsUploadDocument
              :label="document.label"
              @uploaded="updatePayload(document.key, $event)"
            />
          </v-col>

          <v-col cols="12" class="text-right my-1">
            <v-btn @click="dialog = false">close</v-btn>
            <v-btn v-if="formAction == 'Create'" class="primary" @click="submit"
              >Submit</v-btn
            >
            <v-btn
              v-else-if="formAction == 'Edit'"
              class="primary"
              @click="update_data"
              >Update</v-btn
            >
          </v-col>
        </v-row>
      </v-stepper-content>
    </v-stepper>
  </v-dialog>
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  components: {
    VueCropper,
  },

  data: () => ({
    disabled: false,
    step: 1,
    payload: {
      full_name: "",
      phone_number: "",
      floor_id: "",
      room_id: "",
      start_date: "",
      end_date: "",
    },
    documents: [
      { label: "Passport", key: "passport_doc" },
      { label: "ID", key: "id_doc" },
      { label: "Contract", key: "contract_doc" },
      { label: "Ejari", key: "ejari_doc" },
      { label: "License", key: "license_doc" },
      { label: "Other", key: "others_doc" },
    ],
    vehicles: [{ car_number: "", parking_id: "" }],
    imagePreview: "/no-profile-image.jpg",
    setImagePreview: null,
    imageMemberPreview: "/no-profile-image.jpg",

    tab: null,

    totalRowsCount: 0,
    filters: {},
    isFilter: false,
    sortBy: "id",
    sortDesc: false,
    snack: false,
    snackColor: "",
    snackText: "",
    loadinglinear: true,
    displayErrormsg: false,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    dialogCropping: false,
    tabMenu: [],
    tab: "0",
    employeeId: 0,
    employeeObject: {},
    attrs: [],
    dialog: false,
    editDialog: false,
    viewDialog: false,
    selectedFile: "",
    dialog: false,
    memberdialog: false,
    viewMemberdialog: false,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    drawer: true,
    tab: null,
    selectedItem: 1,
    on: "",
    files: "",
    search: "",
    loading: false,
    //total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 1000,
    ListName: "",
    color: "background",
    response: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "Mr",
      display_name: "",
      employee_id: "",
      system_user_id: "",
    },
    upload: {
      name: "",
    },

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Tanent",
    endpoint: "tanent",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    designations: [],
    roles: [],
    employees: [],
    department_filter_id: "",
    dialogVisible: false,
    formAction: "Create",

    date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    menu: false,
    menu2: false,
    menu3: false,

    floors: [],
    rooms: [],
    member: {
      full_name: null,
      phone_number: null,
      age: null,
      relation: null,
      tanent_id: 0,
    },
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;
    await this.getFloors();
  },

  methods: {
    updatePayload(key, document) {
      this.payload[key] = document;
    },
    generateRandomId() {
      const length = 8; // Adjust the length of the ID as needed
      const randomNumber = Math.floor(Math.random() * Math.pow(10, length)); // Generate a random number
      return randomNumber.toString().padStart(length, "0"); // Convert to string and pad with leading zeros if necessary
    },
    handleAttachment(e) {
      this.payload.profile_picture = e;
    },
    nextStep() {
      this.step++;
    },
    prevStep() {
      this.step--;
    },
    getRoomNumber(room_id) {
      let { room_number } = this.rooms.find((e) => e.id == room_id);
      this.payload.room_number = room_number || 0;
    },
    async getFloors() {
      let { data: floors } = await this.$axios.get(`floor`, {
        params: { company_id: this.$auth.user.company_id },
      });
      this.floors = floors.data;
    },
    async getRoomsByFloorId(floor_id) {
      let { floor_number } = this.floors.find((e) => e.id == floor_id);
      this.payload.floor_number = floor_number || 0;

      let { data } = await this.$axios.get(`room-by-floor-id`, {
        params: {
          company_id: this.$auth.user.company_id,
          floor_id: floor_id,
        },
      });
      this.rooms = data;
    },
    closeViewDialog() {
      this.viewDialog = false;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    closePopup() {
      //croppingimagestep5
      this.$refs.otherDoc_input.value = null;
      this.dialogCropping = false;
    },
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    addItem() {
      this.vehicles.push({ car_number: "", parking_id: "" });
    },

    deleteItem(index) {
      this.vehicles.splice(index, 1);
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    previewImage(event) {
      const file = this.payload.profile_picture;

      if (file) {
        // Read the selected file and create a preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        this.imagePreview = null;
      }
    },

    previewMemberImage(event) {
      const file = this.member.profile_picture;

      if (file) {
        // Read the selected file and create a preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imageMemberPreview = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        this.imageMemberPreview = null;
      }
    },
    others_doc(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.otherDoc_input;
      let file = input.files;

      if (file[0].size > 1024 * 1024) {
        e.preventDefault();
        this.errors["profile_picture"] = [
          "File too big (> 1MB). Upload less than 1MB",
        ];
        return;
      }

      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          //croppedimage step6
          // this.previewImage = e.target.result;

          this.selectedFile = event.target.result;

          this.$refs.cropper.replace(this.selectedFile);
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);

        this.dialogCropping = true;
      }
    },
    mapper(obj) {
      let formData = new FormData();

      for (let x in obj) {
        formData.append(x, obj[x]);
      }
      if (this.payload.profile_picture) {
        formData.append("profile_picture", this.upload.name);
      }

      if (this.payload.passport_doc) {
        formData.append("passport_doc", this.payload.passport_doc.name);
      }

      formData.append("company_id", this.$auth.user.company_id);

      return formData;
    },
    tanentValidate() {
      this.$axios
        .post(
          this.endpoint + "-validate",
          this.mapper(Object.assign(this.payload))
        )
        .then(({ data }) => {
          this.errors = [];
          this.nextStep();
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });

      // }
    },

    vehicleValidate() {
      this.$axios
        .post("vehicle-validate", { vehicles: this.vehicles })
        .then(({ data }) => {
          this.errors = [];
          this.nextStep();
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });
    },

    submit() {
      this.$axios
        .post(this.endpoint, this.mapper(Object.assign(this.payload)))
        .then(({ data }) => {
          this.storeVehicle(data.record.id);
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });

      // }
    },

    storeVehicle(id) {
      let dataToInsert = [];

      this.vehicles.forEach(({ car_number, parking_id }) => {
        dataToInsert.push({
          tanent_id: id,
          car_number: car_number,
          parking_id: parking_id,
        });
      });
      this.$axios
        .post("vehicle-store", { vehicles: dataToInsert })
        .then(({ data }) => {
          this.errors = [];
          this.handleSuccessResponse("Tanent inserted successfully");
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });
    },
    handleSuccessResponse(message) {
      this.errors = [];
      this.snackbar = true;
      this.response = message;
      this.memberdialog = false;
      this.dialog = false;
      this.dialog = true;
      this.$emit("success");
    },
    handleErrorResponse(response) {
      if (!response) {
        this.snackbar = true;
        this.response = "An unexpected error occurred.";
        return;
      }
      let { status, data, statusText } = response;

      if (status && status == 422) {
        this.errors = data.errors;
        return;
      }

      this.snackbar = true;
      this.response = statusText;
    },
  },
};
</script>
