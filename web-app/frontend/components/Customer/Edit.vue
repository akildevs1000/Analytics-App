<template>
  <v-dialog persistent v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <span style="cursor: pointer" v-bind="attrs" v-on="on">
        <v-icon right color="black">mdi-account-plus</v-icon>
      </span>
    </template>
    <v-card>
      <v-card-title>Create Maid</v-card-title>
      <v-container>
        <v-row>
          <v-col cols="3">
            <v-row>
              <v-col cols="12">
                <CameraORUpload @imageSrc="handleAttachment" />

                <span
                  v-if="errors && errors.profile_picture"
                  class="text-danger mt-2"
                  >{{ errors.profile_picture[0] }}</span
                >
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="9">
            <v-row>
              <!-- <v-col cols="6">
                <v-autocomplete
                  label="Tanent"
                  outlined
                  v-model="payload.parent_id"
                  :items="tanents"
                  item-value="id"
                  item-text="full_name"
                  dense
                  :hide-details="!errors.parent_id"
                  :error-messages="
                    errors && errors.parent_id ? errors.parent_id[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col> -->
              <v-col cols="6">
                <v-text-field
                  label="Device User Id"
                  :readonly="true"
                  v-model="payload.system_user_id"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.system_user_id"
                  :error-messages="
                    errors && errors.system_user_id
                      ? errors.system_user_id[0]
                      : ''
                  "
                ></v-text-field>
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
                  label="Age"
                  v-model="payload.age"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.age"
                  :error-messages="errors && errors.age ? errors.age[0] : ''"
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
                  label="RFID"
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
                  label="Phone Number"
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
                <v-radio-group
                  class="ma-0 mt-2 px-2 pa-0"
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
            </v-row>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" class="text-right my-1">
            <v-btn @click="dialog = false">Close</v-btn>
            <v-btn class="primary" @click="submit">Submit</v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data: () => ({
    payload: {
      parent_id: ``,
      system_user_id: 111111,
      first_name: "test",
      last_name: "test",
      age: "25",
      member_type: "Maid",
      nationality: "Maid",
      gender: "Male",
      rfid: "2112",
      pin: "0000",
      full_name: "",
      phone_number: "",
    },
    imagePreview: "/no-profile-image.jpg",
    setImagePreview: null,
    imageMemberPreview: "/no-profile-image.jpg",

    snack: false,
    snackColor: "",
    snackText: "",

    dialog: false,
    current_page: 1,
    color: "background",
    response: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    upload: {
      name: "",
    },

    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    response: null,
    errors: [],
    formAction: "Create",
    tanents: [],
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;

    // let { data: tanents } = await this.$axios.get(`tanent-list`, {
    //   params: { company_id: this.$auth.user.company_id },
    // });
    // this.tanents = tanents;
  },

  methods: {
    handleAttachment(e) {
      this.payload.profile_picture = e;
    },

    submit() {
      this.payload.company_id = this.$auth.user.company_id;

      this.$axios
        .post(`add-member`, this.payload)
        .then(({ data }) => {
          this.errors = [];
          this.$emit("response", "Maid inserted successfully");
        })
        .catch(({ response }) => {
          this.response = response?.data?.message ?? null;
          this.errors = response?.data?.errors ?? [];
        });
    },
  },
};
</script>
