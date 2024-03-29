<template>
  <v-dialog persistent v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <span style="cursor: pointer" v-bind="attrs" v-on="on">
        <v-icon color="secondary" small> mdi-pencil </v-icon>
        Edit
      </span>
    </template>
    <v-tabs color="deep-purple accent-4">
      <v-tab>Basic Info</v-tab>
      <v-tab>Members</v-tab>
      <v-tab>Vechicle Info</v-tab>
      <v-tab>Documentation</v-tab>

      <v-tab-item>
        <v-container>
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

                <v-col cols="6">
                  <v-text-field
                    label="Email"
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
                    ></v-date-picker>
                  </v-menu>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    label="Phone Number"
                    v-model="payload.phone_number"
                    dense
                    class="text-center"
                    outlined
                    :hide-details="!errors.phone_number"
                    :error-messages="
                      errors && errors.phone_number
                        ? errors.phone_number[0]
                        : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    label="Whatsapp Number (optional)"
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
                    v-model="payload.rfid"
                    dense
                    class="text-center"
                    outlined
                    :hide-details="!errors.rfid"
                    :error-messages="
                      errors && errors.rfid ? errors.rfid[0] : ''
                    "
                  ></v-text-field>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    label="PIN"
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
                          errors && errors.start_date
                            ? errors.start_date[0]
                            : ''
                        "
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="payload.start_date"
                      @input="menu = false"
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
                    ></v-date-picker>
                  </v-menu>
                </v-col>

                <v-col cols="12">
                  <v-switch
                    style="margin-top: 5px"
                    label="Web Access"
                    outlined
                    v-model="payload.web_access"
                    dense
                    :hide-details="!errors.web_access"
                    :error-messages="
                      errors && errors.web_access ? errors.web_access[0] : ''
                    "
                  >
                  </v-switch>
                </v-col>

                <v-col cols="12" class="text-right my-1">
                  <v-btn @click="dialog = false">Close</v-btn>
                  <v-btn class="primary" @click="update_data">Update</v-btn>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-container>
      </v-tab-item>

      <v-tab-item>
        <v-container>
          <v-row>
            <v-col cols="12" class="text-right mb-5">
              <v-icon color="primary" @click="dialog = false"
                >mdi-close-circle-outline</v-icon
              >
            </v-col>
          </v-row>
          <v-alert
            v-if="!payload.members.length"
            outlined
            color="primary"
            dense
            class="text-center"
          >
            No Member(s) found
          </v-alert>
          <v-card
            v-else
            outlined
            v-for="(member, index) in payload.members"
            :key="index"
            class="mb-2"
          >
            <v-container>
              <v-row>
                <v-col cols="3">
                  <div class="text-center">
                    <SnippetsUploadAttachment
                      :defaultImage="member.profile_picture"
                      @uploaded="
                        (e) => {
                          member.profile_picture = e;
                        }
                      "
                    />

                    <span
                      v-if="errors && errors.logo"
                      class="text-danger mt-2"
                      >{{ errors.logo[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="9">
                  <v-row class="mt-1">
                    <v-col cols="6">
                      <v-text-field
                        label="Full Name"
                        v-model="member.full_name"
                        dense
                        class="text-center"
                        outlined
                        :hide-details="!errors.full_name"
                        :error-messages="
                          errors && errors.full_name ? errors.full_name[0] : ''
                        "
                      ></v-text-field>
                    </v-col>

                    <v-col cols="6">
                      <v-autocomplete
                        label="Member Type"
                        outlined
                        v-model="member.member_type"
                        :items="member_types"
                        dense
                        :hide-details="!errors.member_type"
                        :error-messages="
                          errors && errors.member_type
                            ? errors.member_type[0]
                            : ''
                        "
                      >
                      </v-autocomplete>
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        label="Age"
                        v-model="member.age"
                        dense
                        class="text-center"
                        outlined
                        :hide-details="!errors.age"
                        :error-messages="
                          errors && errors.age ? errors.age[0] : ''
                        "
                      ></v-text-field>
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        label="Phone Number (optional)"
                        v-model="member.phone_number"
                        dense
                        class="text-center"
                        outlined
                        :hide-details="!errors.phone_number"
                        :error-messages="
                          errors && errors.phone_number
                            ? errors.phone_number[0]
                            : ''
                        "
                      ></v-text-field>
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        label="Nationality"
                        v-model="member.nationality"
                        dense
                        class="text-center"
                        outlined
                        :hide-details="!errors.nationality"
                        :error-messages="
                          errors && errors.nationality
                            ? errors.nationality[0]
                            : ''
                        "
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" class="text-right">
                      <v-icon @click="update_member(member)" color="primary"
                        >mdi-floppy</v-icon
                      >
                      <v-icon
                        @click="delete_member(index, member)"
                        color="error"
                        >mdi-delete</v-icon
                      >
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-container>
      </v-tab-item>

      <v-tab-item>
        <v-container>
          <v-row>
            <v-col cols="6">
              <v-icon color="primary" @click="addVehicleInfo"
                >mdi-plus-circle-outline</v-icon
              >
            </v-col>
            <v-col cols="6" class="text-right">
              <v-icon color="primary" @click="dialog = false"
                >mdi-close-circle-outline</v-icon
              >
            </v-col>
          </v-row>
          <!-- <v-alert
            v-if="!payload.vehicles.length"
            outlined
            color="primary"
            dense
            class="text-center"
          >
            No vehicle(s) info found found
          </v-alert> -->
          <v-row v-for="(vehicle, index) in payload.vehicles" :key="index">
            <v-col cols="5">
              <v-text-field
                label="Car Number"
                v-model="vehicle.car_number"
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
                v-model="vehicle.parking_id"
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

            <v-col cols="2" class="text-rights">
              <v-icon @click="delete_vehicle(index)" color="error"
                >mdi-delete</v-icon
              >
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-btn @click="add_vehicles" class="primary">Submit</v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-tab-item>

      <v-tab-item>
        <v-container>
          <v-row>
            <v-col v-for="(document, index) in documents" :key="index" cols="6">
              <SnippetsUploadDocument
                :label="document.label"
                @uploaded="updatePayload(document.key, $event)"
              />
            </v-col>

            <v-col cols="12" class="text-right my-1">
              <v-btn @click="dialog = false">close</v-btn>
              <v-btn class="primary" @click="update_data">Update</v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-tab-item>
    </v-tabs>
  </v-dialog>
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  props: ["item"],
  components: {
    VueCropper,
  },

  data: () => ({
    payload: {
      full_name: "",
      phone_number: "",
      floor_id: "",
      room_id: "",
      start_date: "",
      end_date: "",
      vehicles: [{ car_number: "", parking_id: "" }],
    },
    documents: [
      { label: "Passport", key: "passport_doc" },
      { label: "ID", key: "id_doc" },
      { label: "Contract", key: "contract_doc" },
      { label: "Ejari", key: "ejari_doc" },
      { label: "License", key: "license_doc" },
      { label: "Other", key: "others_doc" },
    ],
    setImagePreview: null,
    tab: null,

    tabMenu: [],
    tab: "0",
    attrs: [],
    selectedFile: "",
    dialog: false,
    right: true,
    tab: null,
    color: "background",
    response: "",
    snackbar: false,
    btnLoader: false,

    upload: {
      name: "",
    },
    options: {},
    Model: "Tanent",
    endpoint: "tanent",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    response: "",
    data: [],
    errors: [],
    member_types:[],

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
      member_type: null,
      tanent_id: 0,
    },
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;
    this.editItem(this.item);
    await this.getFloors();
    await this.getMemberTypes();

  },

  methods: {
    updatePayload(key, document) {
      this.payload[key] = document;
    },
    handleAttachment(e) {
      this.payload.profile_picture = e;
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
    async getMemberTypes() {
      let { data } = await this.$axios.get(`get_member_types`);
      this.member_types = data;
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
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    addVehicleInfo() {
      this.payload.vehicles.push({ car_number: "", parking_id: "" });
    },
    editItem({
      profile_picture,
      passport_doc,
      id_doc,
      contract_doc,
      ejari_doc,
      license_doc,
      others_doc,
      floor,
      room,
      ...payload
    }) {
      this.setImagePreview = profile_picture;
      this.payload = payload;

      this.getRoomsByFloorId(payload.floor_id);
    },

    delete_vehicle(index) {
      this.payload.vehicles.splice(index, 1);
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
    add_vehicles() {
      let dataToInsert = [];

      this.payload.vehicles.forEach(({ car_number, parking_id }) => {
        dataToInsert.push({
          tanent_id: this.payload.id,
          car_number: car_number,
          parking_id: parking_id,
        });
      });
      this.$axios
        .post(`store-multiple-vehicles/${this.payload.id}`, {
          vehicles: dataToInsert,
        })
        .then(({ data }) => {
          this.errors = [];
          this.$emit("success", "Vehicle has been added");
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });
    },
    update_data() {
      this.$axios
        .post(
          this.endpoint + "-update/" + this.payload.id,
          this.mapper(Object.assign(this.payload))
        )
        .then(({ data }) => {
          this.errors = [];
          this.$emit("success", "Tanent has been updated");
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });

      // }
    },

    update_member(member) {
      let formData = new FormData();

      if (member.profile_picture.name) {
        formData.append("profile_picture", member.profile_picture);
      }

      formData.append("full_name", member.full_name);
      formData.append("member_type", member.member_type);
      formData.append("age", member.age);
      formData.append("phone_number", member.phone_number);
      formData.append("nationality", member.nationality);


      this.$axios
        .post("/members-update/" + member.id, formData)
        .then(({ data }) => {
          this.errors = [];
          this.$emit("success", "Member has been updated");
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });

      // }
    },

    delete_member(index, member) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`members/${member.id}`)
          .then(({ data }) => {
            this.payload.members.splice(index, 1);
            this.snackbar = true;
            this.response = "Record deleted successfully";
          })
          .catch((err) => console.log(err));
    },
    handleErrorResponse(response) {
      if (!response) {
        alert("An unexpected error occurred.");
        return;
      }
      let { status, data } = response;

      if (status && status == 422) {
        this.errors = data.errors;
        return;
      }

      alert("An unexpected error occurred.");
    },
  },
};
</script>