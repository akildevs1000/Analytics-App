<template>
  <div style="width: 101%; min-width: 500px">
    <v-col style="margin-top: -7px; width: 99%; padding: 0px">
      <fieldset
        style="
          width: 100%;
          border: 1px solid #a1a1a1;
          border-radius: 11px;
          height: 130px;
        "
      >
        <legend>Employees</legend>
        <!-- <v-card
          class="py-2"
          style="width: 100%; height: 130px; border-radius: 8px"
        > -->
        <div class="text-center" style="width: 100%; margin: auto">
          <v-row class="text-center">
            <v-col cols="1"> </v-col>
            <v-col cols="1" class="pt-0">
              <img
                src="../../static/icons/employee-in.png"
                style="width: 40px"
              />

              <div style="font-size: 30px">{{ data.totalIn ?? "---" }}</div>
              <div style="font-size: 13px">In</div>
            </v-col>
            <v-col cols="1" class="pt-0">
              <img
                src="../../static/icons/employee-out.png"
                style="width: 40px"
              />

              <div style="font-size: 30px">{{ data.totalOut ?? "---" }}</div>
              <div style="font-size: 13px">Out</div>
            </v-col>
            <v-col cols="1" class="pt-0">
              <img
                src="../../static/icons/employee-present.png"
                style="width: 40px"
              />

              <div style="font-size: 30px">
                {{ data.presentCount ?? "---" }}
              </div>
              <div style="font-size: 13px">Present</div>
            </v-col>
            <v-col cols="1" class="pt-0">
              <img
                src="../../static/icons/employee-absent.png"
                style="width: 40px"
              />

              <div style="font-size: 30px">
                {{ data.absentCount ?? "---" }}
              </div>
              <div style="font-size: 14px">Absent</div>
            </v-col>
            <!-- <v-col cols="1" class="pt-0">
              <img src="../../static/icons/holidays.png" style="width: 50px" />

              <div style="font-size: 30px">
                {{ data.holidayCount ?? "---" }}
              </div>
              <div style="font-size: 14px">Holiday</div>
            </v-col> -->
            <v-col cols="1" class="pt-0">
              <img
                src="../../static/icons/employee-leaves.png"
                style="width: 40px"
              />

              <div style="font-size: 30px">
                {{ data.leaveCount ?? "---" }}
              </div>
              <div style="font-size: 14px">Leave</div>
            </v-col>
            <v-col cols="1" class="pt-0">
              <img
                src="../../static/icons/employee-vacation.png"
                style="width: 40px"
              />

              <div style="font-size: 30px">
                {{ data.vaccationCount ?? "---" }}
              </div>
              <div style="font-size: 13px">Vacation</div>
            </v-col>
            <v-col cols="5" class="pt-0">
              <img
                src="../../static/icons/employee-manager.png"
                style="width: 40px"
              />
              <div
                style="margin-top: -13px; font-size: 30px; color: #203864"
                v-if="data.onDutyShiftManager?.employee"
              >
                {{ data.onDutyShiftManager?.employee?.title }}
                {{ data.onDutyShiftManager?.employee?.first_name ?? "---" }}
                {{ data.onDutyShiftManager?.employee?.last_name ?? "---" }}
                <div style="font-size: 10px">
                  Contact:
                  {{ data.onDutyShiftManager?.employee?.phone_number ?? "---" }}
                </div>
              </div>

              <div v-else>---</div>
              <div style="font-size: 13px">Manager On Duty</div>
              <!-- <div
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

          <div v-else>---</div> -->
            </v-col>
          </v-row>
        </div>
        <!-- </v-card> -->
      </fieldset>
    </v-col>
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
