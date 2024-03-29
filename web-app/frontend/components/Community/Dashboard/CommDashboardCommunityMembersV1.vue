<template>
  <div class="bordertop pa-2">
    <v-row>
      <v-col md="10" sm="10" xs="10">
        <h4>Total Community Memebrs</h4>
      </v-col>

      <v-col md="2" sm="2" xs="2" class="text-end"> </v-col>
    </v-row>
    <v-row align-self="center">
      <v-col lg="2" md="2" sm="2" xs="2" align-self="center">
        <v-avatar color="blue">
          <v-icon color="#FFF" size="40">mdi mdi-human-male</v-icon>
        </v-avatar>
      </v-col>

      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 blue--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.male }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Male</v-col
      >
    </v-row>
    <v-row>
      <v-col lg="2" md="2" sm="2" xs="2" class="pt-md-5">
        <v-avatar color="pink">
          <v-icon color="#FFF" size="40">mdi mdi-human-female</v-icon>
        </v-avatar>
      </v-col>

      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 pink--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.female }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Female</v-col
      >
    </v-row>
    <v-row>
      <v-col lg="2" md="2" sm="2" xs="2" class="pt-md-5">
        <v-avatar color="yellow">
          <v-icon color="#FFF" size="40">mdi mdi-teddy-bear</v-icon>
        </v-avatar>
      </v-col>
      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 yellow--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.kids }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Kids</v-col
      >
    </v-row>

    <!-- <v-row>
      <v-col md="12">
        <v-btn
          @click="goToReports()"
          size="small"
          class="btn btn-block fa-lg mt-1 mb-3"
          style="background-color: #6946dd; color: #fff"
        >
          View All reports
        </v-btn>
      </v-col>
    </v-row> -->
  </div>
</template>
<script>
export default {
  props: ["branch_id"],
  data: () => ({
    options: {},

    loading: false,
    dataLength: 0,

    data: {
      male: 0,
      female: 0,
      kids: 0,
    },
  }),
  watch: {
    branch_id() {
      this.$store.commit("dashboard/community_male_female", null);
      this.getDataFromApi();
    },
  },
  mounted() {
    this.$store.commit("dashboard/community_male_female", null);
    this.getDataFromApi();
  },
  created() {
    let today = new Date();

    this.date_to = today.toISOString().split("T")[0];
    this.date_from = today.toISOString().split("T")[0];
    // this.getDataFromApi();
  },

  methods: {
    goToReports() {
      this.$router.push("/attendance_report");
    },

    viewLogs() {
      this.$router.push("/attendance_report");
    },
    async getDataFromApi() {
      if (this.$store.state.community_male_female) {
        this.data = this.$store.state.community_male_female;
      } else {
        this.$axios
          .get("community_dashboard_get_male_female_count", {
            params: {
              company_id: this.$auth.user.company_id,
              branch_id: this.branch_id > 0 ? this.branch_id : null,
            },
          })
          .then(({ data }) => {
            this.data = data;

            this.$store.commit("CommDashboard/community_male_female", data);
          });
      }
    },
  },
};
</script>

<style scoped>
.center-both {
  height: 31vh;
  /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 500px) {
  .bordertop {
    border-top: 1px solid #ddd;
    padding-bottom: 5px;
    border-left: 0px;
  }
}

@media (max-width: 1500px) {
  .laptop-padding {
    padding-left: 30px;
  }
}
</style>
