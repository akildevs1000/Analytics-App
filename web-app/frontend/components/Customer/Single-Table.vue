<template>
  <v-dialog persistent v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <span style="cursor: pointer" v-bind="attrs" v-on="on">
        <v-icon color="black" small> mdi-eye </v-icon>
        View
      </span>
    </template>
    <v-card>
      <v-card-title
        >View Customer
        <v-spacer />
        <v-icon color="primary" @click="dialog = false"
          >mdi-close</v-icon
        ></v-card-title
      >
      <v-container>
        <v-row>
          <v-col cols="3">
            <v-row>
              <v-col cols="12">
                <ViewProfilePicture
                  :PreviewImage="defaultImage"
                  @imageSrc="handleAttachment"
                />

                <span
                  v-if="errors && errors.profile_picture"
                  class="text-danger mt-2"
                  >{{ errors.profile_picture[0] }}</span
                >
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="9">
            <table>
              <tr>
                <th>Customer Type</th>
                <td>
                  {{ payload.type }}
                </td>
              </tr>
              <tr>
                <th>Device User Id</th>
                <td>
                  {{ payload.system_user_id }}
                </td>
              </tr>
              <tr>
                <th>First Name</th>
                <td>
                  {{ payload.first_name }}
                </td>
              </tr>
              <tr>
                <th>Last Name</th>
                <td>
                  {{ payload.last_name }}
                </td>
              </tr>
              <tr>
                <th>Phone Number</th>
                <td>
                  {{ payload.phone_number }}
                </td>
              </tr>
              <tr>
                <th>Status</th>
                <td>
                  {{ payload.status }}
                </td>
              </tr>
            </table>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
import ViewProfilePicture from "../ViewProfilePicture.vue";

export default {
  props: ["item"],
  data: () => ({
    payload: {},
    imagePreview: "/no-profile-image.jpg",
    setImagePreview: null,
    imageMemberPreview: "/no-profile-image.jpg",
    dialog: false,
    ids: [],
    defaultImage: false,
    loading: false,
    response: null,
    errors: [],
    endpoint: `customer`,
  }),

  async created() {
    this.defaultImage = this.item.profile_picture;

    this.payload = this.item;

    delete this.payload.profile_picture;
  },

  methods: {
    handleAttachment(e) {
      this.payload.profile_picture = e;
    },

    submit() {
      this.payload.full_name =
        this.payload.first_name + " " + this.payload.last_name;
      this.payload.company_id = this.$auth.user.company_id;
      this.$axios
        .put(this.endpoint + "/" + this.item.id, this.payload)
        .then(({ data }) => {
          this.errors = [];
          this.$emit("response", "Customer update successfully");
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
<style scoped>
@import url("../../assets/tableStyles.css");
</style>
