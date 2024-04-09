<template>
  <v-dialog persistent v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <span style="cursor: pointer" v-bind="attrs" v-on="on">
        <v-icon right color="black">mdi-account-plus</v-icon>
      </span>
    </template>
    <v-card>
      <v-card-title
        >Create Customer<v-spacer />
        <v-icon color="primary" @click="dialog = false"
          >mdi-close</v-icon
        ></v-card-title
      >
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
              <v-col cols="6">
                <v-autocomplete
                  label="Customer"
                  outlined
                  v-model="payload.type"
                  :items="[
                    { id: `VIP`, name: `VIP` },
                    { id: `NORMAL`, name: `NORMAL` },
                  ]"
                  item-value="id"
                  item-text="name"
                  dense
                  :hide-details="!errors.type"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                >
                </v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="Device User Id"
                  v-model="payload.system_user_id"
                  dense
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
                <v-autocomplete
                  label="Status"
                  outlined
                  v-model="payload.status"
                  :items="[
                    { id: `whitelisted`, name: `Whitle List` },
                    { id: `blocklisted`, name: `Block List` },
                  ]"
                  item-value="id"
                  item-text="name"
                  dense
                  :hide-details="!errors.type"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                >
                </v-autocomplete>
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
    payload: {},
    imagePreview: "/no-profile-image.jpg",
    setImagePreview: null,
    imageMemberPreview: "/no-profile-image.jpg",
    dialog: false,
    ids: [],
    loading: false,
    response: null,
    errors: [],
    endpoint: `customer`,
  }),

  async created() {},

  methods: {
    handleAttachment(e) {
      this.payload.profile_picture = e;
    },

    submit() {
      this.payload.full_name =
        this.payload.first_name + " " + this.payload.last_name;
      this.payload.company_id = this.$auth.user.company_id;
      this.$axios
        .post(this.endpoint, this.payload)
        .then(({ data }) => {
          this.errors = [];
          this.$emit("response", "Customer inserted successfully");
          this.dialog = false;
        })
        .catch(({ response }) => {
          this.response = response?.data?.message ?? null;
          this.errors = response?.data?.errors ?? [];
        });
    },
  },
};
</script>
