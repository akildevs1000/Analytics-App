<template>
  <div class="bordertop">
    <v-row>
      <v-col md="10" sm="10" xs="10">
        <h4>Statistics</h4>
      </v-col>

      <v-col md="2" sm="2" xs="2" class="text-end">
        <!-- <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item @click="viewLogs()">
              <v-list-item-title style="cursor: pointer">
                View Logs
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu> -->
      </v-col>
    </v-row>
    <v-row align-self="center">
      <v-col lg="2" md="2" sm="2" xs="2" align-self="center">
        <v-avatar color="#14B012">
          <v-icon color="#FFF">mdi mdi-home-silo-outline</v-icon>
        </v-avatar>
      </v-col>

      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 red--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.TenantCount }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Tenants</v-col
      >
    </v-row>
    <v-row>
      <v-col lg="2" md="2" sm="2" xs="2" class="pt-md-5">
        <v-avatar color="#FFB600">
          <v-icon color="#FFF">mdi mdi-account-details</v-icon>
        </v-avatar>
      </v-col>

      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 blue--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.VisitorCount }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Visitors</v-col
      >
    </v-row>
    <v-row>
      <v-col lg="2" md="2" sm="2" xs="2" class="pt-md-5">
        <v-avatar color="#2196F3">
          <v-icon color="#FFF">mdi mdi-account-tie</v-icon>
        </v-avatar>
      </v-col>
      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 orange--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.EmployeeCount }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Employees</v-col
      >
    </v-row>
    <v-row>
      <v-col lg="2" md="2" sm="2" xs="2" class="pt-md-5">
        <v-avatar color="#FF0000">
          <v-icon color="#FFF">mdi mdi-account-remove</v-icon>
        </v-avatar>
      </v-col>
      <v-col
        lg="4"
        md="4"
        sm="4"
        xs="4"
        class="text-red bold text-h3 orange--text text-center laptop-padding"
        align-self="center"
        >{{ data && data.DeniedCount }}</v-col
      >
      <v-col lg="6" md="6" sm="6" xs="6" class=" " align-self="center"
        >Access Denied</v-col
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
      date: " ",
      EmployeeCount: 0,
      VisitorCount: 0,
      TenantCount: 0,
      DeniedCount: 0,
    },
  }),
  watch: {
    branch_id() {
      this.$store.commit("dashboard/previous_week_attendance_count", null);
      this.getDataFromApi();
    },
  },
  mounted() {
    this.getDataFromApi();
  },
  created() {
    let today = new Date();

    this.date_to = today.toISOString().split("T")[0];
    this.date_from = today.toISOString().split("T")[0];
    this.getDataFromApi();
  },

  methods: {
    goToReports() {
      this.$router.push("/attendance_report");
    },

    viewLogs() {
      this.$router.push("/attendance_report");
    },
    async getDataFromApi() {
      this.$store.dispatch("CommDashboard/setDates", {
        date_from: this.date_from,
        date_to: this.date_to,
        branch_id: null,
        system_user_id: null,
        device_id: null,
      });
      this.data = await this.$store.dispatch("CommDashboard/today_statistics"); //this.$store.state.CommDashboard.today_statistics;

      // if (this.$store.state.dashboard.previous_week_attendance_count) {
      //   this.data = this.$store.state.dashboard.previous_week_attendance_count;
      //   return;
      // }
      // let options = {
      //   params: {
      //     branch_id: this.branch_id > 0 ? this.branch_id : null,
      //   },
      // };
      // this.$axios
      //   .get(
      //     `previous_week_attendance_count/${this.$auth.user.company_id}`,
      //     options
      //   )
      //   .then(({ data }) => {
      //     this.data = data;
      //     this.$store.commit("dashboard/previous_week_attendance_count", data);
      //   })
      //   .catch(({ message }) => console.log(message));
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
