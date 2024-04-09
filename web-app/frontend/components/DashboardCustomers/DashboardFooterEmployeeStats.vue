<template>
  <div style="width: 101%; min-width: 500px">
    <div class="text-center" style="width: 100%; margin: auto">
      <v-row class="text-center">
        <v-col cols="1"> </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">In</div>
          <div style="font-size: 40px">{{ data.totalIn ?? "---" }}</div>
        </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">Out</div>
          <div style="font-size: 40px">{{ data.totalOut ?? "---" }}</div>
        </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">Present</div>
          <div style="font-size: 40px">{{ data.presentCount ?? "---" }}</div>
        </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">Absent</div>
          <div style="font-size: 40px">{{ data.absentCount ?? "---" }}</div>
        </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">Holiday</div>
          <div style="font-size: 40px">{{ data.holidayCount ?? "---" }}</div>
        </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">Leave</div>
          <div style="font-size: 40px">{{ data.leaveCount ?? "---" }}</div>
        </v-col>
        <v-col cols="1">
          <div style="font-size: 20px">Vacation</div>
          <div style="font-size: 40px">{{ data.vaccationCount ?? "---" }}</div>
        </v-col>
        <v-col cols="4">
          <div>Manager On Duty</div>
          <div
            style="font-size: 30px; color: #203864"
            v-if="data.branchDetails?.user"
          >
            {{ data.branchDetails?.user?.employee?.title }}
            {{ data.branchDetails?.user?.employee?.first_name ?? "---" }}
            {{ data.branchDetails?.user?.employee?.last_name ?? "---" }}
            <div style="font-size: 10px">
              {{ data.branchDetails?.user?.employee?.phone_number ?? "---" }}
            </div>
          </div>

          <div v-else>---</div>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
export default {
  components: {},
  props: ["filter_from_date"],
  data() {
    return {
      device_serial_number: "",
      from_date: "",
      from_date: "",
      from_menu: false,
      topMenu: 0,
      devicesList: [],
      data: {},
    };
  },
  watch: {},
  mounted() {},
  async created() {
    if (this.$store.state.dashboard.customerDashboardEmployeeData)
      this.data = this.$store.state.dashboard.customerDashboardEmployeeData;
    setInterval(() => {
      if (this.$store.state.dashboard.customerDashboardEmployeeData)
        this.data = this.$store.state.dashboard.customerDashboardEmployeeData;
    }, 1000);
  },
  // watch: {
  //   overlay(val) {
  //     val &&
  //       setTimeout(() => {
  //         this.overlay = false;
  //       }, 3000);
  //   },
  // },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    ChangeDevice(serial_number) {
      try {
        this.device_serial_number = serial_number;
        // this.key++;
        // this.keyChart2++;

        //this.getDataFromApi(1);
        //console.log(this.device_serial_number, " this.device_serial_number");
      } catch (e) {}
    },
  },
};
</script>
