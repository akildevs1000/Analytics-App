<template>
  <div class="mt-2 pl-2">
    <v-row>
      <v-col cols="10" md="10" sm="10" xs="10" style="flex: auto">
        <h4>Today Presents By Department wise</h4>
      </v-col>

      <v-col md="2" sm="2" xs="2" class="text-end">
        <v-menu bottom left>
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
        </v-menu>
      </v-col>
    </v-row>
    <ComonPreloader icon="face-scan" v-if="loading" />
    <v-row v-else style="margin: 0px">
      <v-col md="2" class="text-center"># </v-col>
      <v-col md="5">&nbsp; </v-col>
      <v-col md="1" title="Present Count" style="text-align: center">P</v-col>
      <v-col md="1" title="Absent Count" style="text-align: center">A </v-col>
      <v-col md="1" title="Missing Count" style="text-align: center">M </v-col>
      <!-- <v-col md="1" title="absentCount" style="text-align: center">V </v-col> -->
      <v-col md="1" title="Missing Count" style="text-align: center">L </v-col>
    </v-row>
    <div style="height: 250px; overflow-y: auto; overflow-x: hidden">
      <v-row v-for="(item, index, i) in departments" :key="'department' + i">
        <v-col md="2" class="text-center">
          <v-avatar
            size="30"
            style="color: #fff"
            :color="(i + 1) % 2 == 0 ? 'green' : 'red'"
          >
            <v-icon size="20" style="color: #fff">mdi-laptop</v-icon>
          </v-avatar>
        </v-col>
        <v-col md="5" class="mt-2" style="overflow: hidden">{{ index }} </v-col>
        <v-col md="1" title="Presents" style="color: green; text-align: center">
          {{ item.presentCount }}
        </v-col>
        <v-col md="1" title="Absents" style="color: red; text-align: center">{{
          item.absentCount
        }}</v-col>
        <v-col md="1" title="Missing" style="color: orange; text-align: center"
          >{{ item.missingCount }}
        </v-col>
        <!-- <v-col md="1" title="Vacation" style="text-align: center"
        >{{ item.vaccationCount }}
      </v-col> -->
        <v-col md="1" title="Leave" style="text-align: center"
          >{{ item.leaveCount }}
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  data() {
    return {
      departments: [],
      loading: false,
    };
  },
  watch: {},
  created() {},
  mounted() {
    this.loading = true;
    setTimeout(() => {
      this.getDataFromApi();
    }, 1000 * 4);
  },

  methods: {
    viewLogs() {
      this.$router.push("/attendance_report");
    },
    getDataFromApi() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios
        .get("dashboard_get_count_department", options)
        .then(({ data }) => {
          this.departments = data;

          this.loading = false;
        });
    },
  },
};
</script>
