<template>
  <v-dialog persistent v-model="dialog" width="800">
    <template v-slot:activator="{ on, attrs }">
      <span style="cursor: pointer" v-bind="attrs" v-on="on">
        <v-icon color="secondary" small> mdi-account </v-icon>
        Add Member(s)
      </span>
    </template>
    <v-card>
      <v-toolbar dense class="popup_background" flat>
        {{ formAction }} Member
        <v-spacer></v-spacer>
      </v-toolbar>

      <v-container>
        <v-row no-gutters>
          <v-col cols="4" class="text-center pt-3">
            <v-avatar size="150" style="border: 1px solid #6946dd">
              <v-img :src="imageMemberPreview"></v-img>
            </v-avatar>
          </v-col>
          <v-col cols="8" class="pt-2">
            <v-row>
              <v-col cols="6">
                <v-text-field
                  label="Full Name"
                  v-model="member.full_name"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="true"
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
                    errors && errors.member_type ? errors.member_type[0] : ''
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
                  :error-messages="errors && errors.age ? errors.age[0] : ''"
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
                    errors && errors.phone_number ? errors.phone_number[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-file-input
                  v-model="member.profile_picture"
                  dense
                  outlined
                  prepend-icon=""
                  append-icon="mdi-camera"
                  label="Upload Photo"
                  @change="previewMemberImage"
                ></v-file-input>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="Nationality"
                  v-model="member.nationality"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="true"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-container>
      <v-divider></v-divider>
      <v-card-actions>
        <v-spacer></v-spacer>
        <div class="text-right">
          <v-btn small color="grey white--text" @click="dialog = false">
            Close
          </v-btn>

          <v-btn
            small
            :loading="loading"
            color="primary"
            @click="submitMembers"
          >
            submit
          </v-btn>
        </div>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: ["item"],

  data: () => ({
    dialog: false,
    imagePreview: "/no-profile-image.jpg",
    setImagePreview: null,
    imageMemberPreview: "/no-profile-image.jpg",
    loading: false,
    color: "background",
    response: "",
    errors: [],
    formAction: "Create",
    member_types: [],
    member: {
      full_name: null,
      age: null,
      member_type: null,
      nationality: null,
      tanent_id: 0,
    },
  }),

  async created() {
    this.loading = false;

    await this.getMemberTypes();

    this.member = {
      ...this.member,
      ...this.item,
    };
  },
  methods: {
    generateRandomId() {
      const length = 8; // Adjust the length of the ID as needed
      const randomNumber = Math.floor(Math.random() * Math.pow(10, length)); // Generate a random number
      return randomNumber.toString().padStart(length, "0"); // Convert to string and pad with leading zeros if necessary
    },
    async getMemberTypes() {
      let { data } = await this.$axios.get(`get_member_types`);
      this.member_types = data;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
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

    mapper(obj) {
      let formData = new FormData();

      for (let x in obj) {
        formData.append(x, obj[x]);
      }

      return formData;
    },
    submitMembers() {
      this.$axios
        .post(
          `/members/${this.member.tanent_id}`,
          this.mapper(Object.assign(this.member))
        )
        .then(({ data }) => {
          this.errors = [];
          this.$emit("success", "Member(s) inserted successfully");
        })
        .catch(({ response }) => {
          this.handleErrorResponse(response);
        });
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
