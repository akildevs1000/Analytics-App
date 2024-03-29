<template>
  <div>
    <v-row>
      <v-col md="6">
        <v-card class="py-2 mt-2" style="height: 230px">
          <v-row class="p-2">
            <v-row>
              <v-col md="6">
                <v-avatar color="#FFCDD2">
                  <v-icon>mdi-account-details</v-icon>
                </v-avatar>
              </v-col>

              <v-col md="6" class="menu-icon-right">
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
            <v-row>
              <v-col md="12" class="bold" style="text-align: center">
                <div style="font-weight: bold" class="bold">Multi In/Out</div>
                <div style="font-weight: bold" class="text-h2 green--text bold">
                  {{ multi_presents }}
                </div>
                <div>Today Presents</div>
              </v-col>
            </v-row>
          </v-row>
        </v-card>
      </v-col>

      <v-col md="6">
        <v-card class="py-2 mt-2" style="height: 230px">
          <v-row class="p-2">
            <v-row>
              <v-col md="6">
                <v-avatar color="#FFB74D">
                  <v-icon> mdi-account-arrow-right</v-icon>
                </v-avatar>
              </v-col>

              <v-col md="6" class="menu-icon-right">
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
            <v-row>
              <v-col md="12" class="bold" style="text-align: center">
                <div style="font-weight: bold">General and Others</div>
                <div style="font-weight: bold" class="text-h2 green--text bold">
                  {{ general_presents }}
                </div>
                <div>Today Presents</div>
              </v-col>
            </v-row>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    options: {},
    Model: "Announcement",
    endpoint: "announcement_list",
    loading: false,
    dataLength: 0,

    multi_presents: 0,
    general_presents: 0,

    data: [],
  }),
  mounted() {
    setTimeout(() => {
      this.getDataFromApi();
    }, 1000 * 5);
  },
  created() {},

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
        .get("dashboard_Get_Counts_today_multi_general", options)
        .then(({ data }) => {
          this.multi_presents = data.multi.presentCount;
          this.general_presents = data.general.presentCount;
        });
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
</style>
