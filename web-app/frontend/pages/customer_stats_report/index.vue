<template>
  <div v-if="can(`attendance_report_view`)">
    <v-card elevation="0" class="mt-2">
      <v-toolbar dense flat>
        <span class="headline black--text"> Customer Reports </span>
        <v-spacer></v-spacer>
        <v-row style="text-align: right" justify="end">
          <v-col cols="3">
            <v-select
              style="width: 100%"
              label="Branch"
              outlined
              dense
              v-model="payload.branch_id"
              x-small
              :items="[{ id: ``, branch_name: `All Branches` }, ...branches]"
              item-value="id"
              item-text="branch_name"
              :hide-details="true"
            >
            </v-select>
          </v-col>

          <v-col cols="3" style="text-align: left">
            <CustomFilter
              @filter-attr="filterAttr"
              :defaultFilterType="1"
              :height="'40px'"
            />
          </v-col>
          <v-col cols="1" style="text-align: left">
            <v-btn
              @click="getDataFromApi()"
              style="margin-left: -10px"
              color="primary"
              primary
              fill
              >Submit
            </v-btn>
          </v-col>
        </v-row>
      </v-toolbar>

      <v-card-text class="py-3"> </v-card-text>
    </v-card>
    <v-row class="mt-2 ml-1">
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Highest Footfall
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #0f9d58;
                "
                >Weekday</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <!-- <img
                        src="../../static/icons/foot.png"
                        style="width: 35px"
                      /> -->
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-account-group</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                    >{{ highestCounts.footFall.count ?? "---" }}</span
                  >
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >{{ highestCounts.footFall.date ?? "---" }}
                    {{
                      caps(
                        $dateFormat.getDayFullName(highestCounts.footFall.date)
                      )
                    }}</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12" style="padding-right: 0px">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Lowest Footfall
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #0f9d58;
                "
                >Weekday</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-account-group</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                    >{{ highestCounts.lowestFootfall.count ?? "---" }}
                  </span>
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >{{ highestCounts.lowestFootfall.date ?? "---" }}
                    {{
                      caps(
                        $dateFormat.getDayFullName(
                          highestCounts.lowestFootfall.date
                        )
                      )
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Peak Hours
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #0f9d58;
                "
                >Weekday</span
              >
            </div>
            <v-row style="height: 115px">
              <v-col
                cols="12"
                style="
                  font-size: 50px;
                  color: #c55a11;
                  text-align: center;
                  margin: auto;
                "
              >
                <span style="">
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 30px;
                    "
                    v-if="peakHoursListWeekDays.length == 0"
                    >---</span
                  >
                  <div
                    v-for="hour in peakHoursListWeekDays"
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 30px;
                    "
                  >
                    {{ $dateFormat.convertToAMPM(hour) }} -
                    {{ $dateFormat.convertToAMPM(hour + 1) }}
                  </div>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Lean Hours
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #0f9d58;
                "
                >Weekday</span
              >
            </div>
            <v-row style="height: 115px">
              <v-col
                cols="12"
                style="
                  margin: auto;
                  font-size: 50px;
                  color: #c55a11;
                  text-align: center;
                "
              >
                <span style="">
                  <span style="">
                    <span
                      style="
                        margin: auto;
                        padding: 0px;
                        margin-left: 0px;
                        font-weight: 300;
                        font-size: 30px;
                      "
                      v-if="leanHoursListWeekDays.length == 0"
                      >---</span
                    >
                    <div
                      v-for="hour in leanHoursListWeekDays"
                      style="
                        margin: auto;
                        padding: 0px;
                        margin-left: 0px;
                        font-weight: 300;
                        font-size: 30px;
                      "
                    >
                      {{ $dateFormat.convertToAMPM(hour) }} -
                      {{ $dateFormat.convertToAMPM(hour + 1) }}
                    </div>
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Top Age Group
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #0f9d58;
                "
                >Weekday</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 30px;
                    "
                    >{{ highestCounts.ageGroup.name ?? "---" }} ({{
                      highestCounts.ageGroup.count ?? "---"
                    }})</span
                  >
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                  >
                    {{ highestCounts.ageGroup.date }}
                    {{
                      caps(
                        $dateFormat.getDayFullName(highestCounts.ageGroup.date)
                      )
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Highest Time Spent<span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #0f9d58;
                "
                >Weekday</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-clock-outline</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                  >
                    {{
                      highestCounts.timeSpent.count >= 0
                        ? $dateFormat.minutesToHHMM(
                            Math.round(highestCounts.timeSpent.count)
                          )
                        : "---"
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >{{ highestCounts.timeSpent.date ?? "---" }}

                    {{
                      caps(
                        $dateFormat.getDayFullName(highestCounts.timeSpent.date)
                      )
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
    </v-row>

    <v-row class="mt-5 ml-1">
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Highest Footfall
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #4285f4;
                "
                >Weekend</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <!-- <img
                        src="../../static/icons/foot.png"
                        style="width: 35px"
                      /> -->
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-account-group</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                    >{{ highestCountsWeekend.footFall.count ?? "---" }}</span
                  >
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >{{ highestCountsWeekend.footFall.date ?? "---" }}
                    {{
                      caps(
                        $dateFormat.getDayFullName(
                          highestCountsWeekend.footFall.date
                        )
                      )
                    }}</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12" style="padding-right: 0px">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Lowest Footfall
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #4285f4;
                "
                >Weekend</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-account-group</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                    >{{ highestCountsWeekend.lowestFootfall.count ?? "---" }}
                  </span>
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >{{ highestCountsWeekend.lowestFootfall.date ?? "---" }}
                    {{
                      caps(
                        $dateFormat.getDayFullName(
                          highestCountsWeekend.lowestFootfall.date
                        )
                      )
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Peak Hours
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #4285f4;
                "
                >Weekend</span
              >
            </div>
            <v-row style="height: 115px">
              <v-col
                cols="12"
                style="
                  font-size: 50px;
                  color: #c55a11;
                  text-align: center;
                  margin: auto;
                "
              >
                <span style="">
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 30px;
                    "
                    v-if="peakHoursListWeekEnds.length == 0"
                    >---</span
                  >
                  <div
                    v-for="hour in peakHoursListWeekEnds"
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 30px;
                    "
                  >
                    {{ $dateFormat.convertToAMPM(hour) }} -
                    {{ $dateFormat.convertToAMPM(hour + 1) }}
                  </div>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Lean Hours
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #4285f4;
                "
                >Weekend</span
              >
            </div>
            <v-row style="height: 115px">
              <v-col
                cols="12"
                style="
                  margin: auto;
                  font-size: 50px;
                  color: #c55a11;
                  text-align: center;
                "
              >
                <span style="">
                  <span style="">
                    <span
                      style="
                        margin: auto;
                        padding: 0px;
                        margin-left: 0px;
                        font-weight: 300;
                        font-size: 30px;
                      "
                      v-if="leanHoursListWeekEnds.length == 0"
                      >---</span
                    >
                    <div
                      v-for="hour in leanHoursListWeekEnds"
                      style="
                        margin: auto;
                        padding: 0px;
                        margin-left: 0px;
                        font-weight: 300;
                        font-size: 30px;
                      "
                    >
                      {{ $dateFormat.convertToAMPM(hour) }} -
                      {{ $dateFormat.convertToAMPM(hour + 1) }}
                    </div>
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Top Age Group
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #4285f4;
                "
                >Weekend</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 30px;
                    "
                    >{{ highestCountsWeekend.ageGroup.name ?? "---" }} ({{
                      highestCountsWeekend.ageGroup.count ?? "---"
                    }})</span
                  >
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                  >
                    {{ highestCountsWeekend.ageGroup.date }}
                    {{
                      caps(
                        $dateFormat.getDayFullName(
                          highestCountsWeekend.ageGroup.date
                        )
                      )
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Highest Time Spent
              <span
                style="
                  font-size: 12px;
                  float: right;
                  padding-right: 5px;
                  color: #4285f4;
                "
                >Weekend</span
              >
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-clock-outline</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                  >
                    {{
                      highestCountsWeekend.timeSpent.count >= 0
                        ? $dateFormat.minutesToHHMM(
                            Math.round(highestCountsWeekend.timeSpent.count)
                          )
                        : "---"
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <v-icon size="20">mdi mdi-calendar-range</v-icon>
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >{{ highestCountsWeekend.timeSpent.date ?? "---" }}

                    {{
                      caps(
                        $dateFormat.getDayFullName(
                          highestCountsWeekend.timeSpent.date
                        )
                      )
                    }}
                  </span>
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
    </v-row>
    <v-card class="mb-5 mt-5" elevation="0">
      <div v-if="can(`attendance_report_access`)">
        <div class="text-center">
          <v-snackbar
            v-model="snackbar"
            top="top"
            color="secondary"
            elevation="24"
          >
            {{ response }}
          </v-snackbar>
          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
        </div>
        <v-card class="mb-5" elevation="0">
          <v-toolbar class="backgrounds" dense flat>
            <v-toolbar-title> </v-toolbar-title>

            <v-spacer></v-spacer>
            <!-- <span style="padding-left: 15px"
              ><img
                title="Print"
                style="cursor: pointer"
                @click="process_file('customer-report-print')"
                src="/icons/icon_print.png"
                class="iconsize"
            /></span>
            <span style="padding-left: 15px"
              ><img
                title="Download Pdf"
                style="cursor: pointer"
                @click="process_file('customer-report-download')"
                src="/icons/icon_pdf.png"
                class="iconsize"
            /></span> -->
          </v-toolbar>

          <v-data-table
            dense
            :headers="headers"
            :items="data"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            model-value="data.id"
            :server-items-length="totalRowsCount"
            fixed-header
            :height="tableHeight"
          >
            <template v-slot:item.id="{ item, index }">
              {{ index + 1 }}
            </template>

            <template v-slot:item.total_footfall="{ item, index }">
              <div>{{ item.in_count + item.out_count }}</div>
              <div>
                <span>{{ item.male_count }}</span
                ><span style="font-size: 10px">M</span>

                <span>{{ item.female_count }}</span
                ><span style="font-size: 10px">F</span>
                <span>{{ item.child_count }}</span
                ><span style="font-size: 10px">K</span>
              </div>
            </template>
            <template v-slot:item.in="{ item, index }">
              <div>{{ item.in_count }}</div>
              <div>
                <span>{{ item.in_male_count }}</span
                ><span style="font-size: 10px">M</span>
                <span>{{ item.in_female_count }}</span
                ><span style="font-size: 10px">F</span>
                <span>{{ item.in_child_count }}</span
                ><span style="font-size: 10px">K</span>
              </div>
            </template>
            <template v-slot:item.out="{ item, index }">
              <div>{{ item.out_count }}</div>
              <div>
                <span>{{ item.out_male_count }}</span
                ><span style="font-size: 10px">M</span>
                <span>{{ item.out_female_count }}</span
                ><span style="font-size: 10px">F</span>
                <span>{{ item.out_child_count }}</span
                ><span style="font-size: 10px">K</span>
              </div>
            </template>
            <template v-slot:item.occupancy="{ item, index }">
              <div>
                {{
                  item.occupancy > 0
                    ? Math.round(
                        ((item.in_count + item.out_count) * 100) /
                          item.occupancy
                      )
                    : 0
                }}%
              </div>
            </template>
            <template v-slot:item.avg_time="{ item, index }">
              <div>
                {{
                  item.avg_total_hours
                    ? $dateFormat.minutesToHHMM(
                        Math.round(item.avg_total_hours)
                      )
                    : "---"
                }}
              </div>
            </template>
            <template v-slot:item.min_time="{ item, index }">
              <div>
                {{
                  item.min_total_hours
                    ? $dateFormat.minutesToHHMM(
                        Math.round(item.min_total_hours)
                      )
                    : "---"
                }}
              </div>
            </template>
            <template v-slot:item.max_time="{ item, index }">
              <div>
                {{
                  item.max_total_hours
                    ? $dateFormat.minutesToHHMM(
                        Math.round(item.max_total_hours)
                      )
                    : "---"
                }}
              </div>
            </template>
            <template v-slot:item.male="{ item, index }">
              <div>{{ item.male_count }}</div>
              <div>
                <span>{{ item.male_adult_count }}</span
                ><span style="font-size: 10px">A</span>
                <span>{{ item.male_younger_count }}</span
                ><span style="font-size: 10px">Y</span>
                <span>{{ item.male_senior_count }}</span
                ><span style="font-size: 10px">S</span>
              </div>
            </template>
            <template v-slot:item.female="{ item, index }">
              <div>{{ item.female_count }}</div>
              <div>
                <span>{{ item.female_adult_count }}</span
                ><span style="font-size: 10px">A</span>
                <span>{{ item.female_younger_count }}</span
                ><span style="font-size: 10px">Y</span>
                <span>{{ item.female_senior_count }}</span
                ><span style="font-size: 10px">S</span>
              </div>
            </template>
            <template v-slot:item.child="{ item, index }">
              <div>{{ item.child_count }}</div>
              <div>
                <span>{{ item.child_male_count }}</span
                ><span style="font-size: 10px">M</span>
                <span>{{ item.child_female_count }}</span
                ><span style="font-size: 10px">F</span>
              </div>
            </template>
            <template v-slot:item.vip="{ item, index }">
              <div>{{ item.vip_customer_count }}</div>
            </template>
            <template v-slot:item.repeated="{ item, index }">
              <div>{{ item.repeated_customer_count }}</div>
            </template>
            <template v-slot:item.blocked="{ item, index }">
              <div>{{ item.blocklisted_customer_count }}</div>
            </template>
          </v-data-table>
        </v-card>
      </div>
    </v-card>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  props: [],

  data: () => ({
    peakHoursListWeekDays: [],
    leanHoursListWeekDays: [],
    peakHoursListWeekEnds: [],
    leanHoursListWeekEnds: [],
    tableHeight: 750,
    status: "",
    department_ids: "",
    employee_id: "",
    daily_date: "",
    to_date: "",

    isFilter: false,
    totalRowsCount: 0,
    snack: false,
    snackColor: "",
    snackText: "",
    date: null,
    menu: false,
    options: {},
    loading: false,
    time_menu: false,
    Model: "Attendance Reports",
    endpoint: "customer-stats-report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    generateLogsDialog: false,
    reportSync: false,
    from_menu: false,
    to_menu: false,
    ids: [],
    departments: [],
    employees: [],
    DateRange: true,
    devices: [],

    total: 0,

    highestCounts: {
      footFall: {
        count: null,
        date: null,
      },
      maleCount: {
        count: null,
        date: null,
      },
      femaleCount: {
        count: null,
        date: null,
      },
      ageGroup: {
        name: null,
        count: null,
        date: null,
      },
      timeSpent: {
        count: null,
        date: null,
      },
      blockListed: {
        count: null,
        date: null,
      },
      lowestFootfall: {
        count: null,
        date: null,
      },
    },

    highestCountsWeekend: {
      footFall: {
        count: null,
        date: null,
      },
      maleCount: {
        count: null,
        date: null,
      },
      femaleCount: {
        count: null,
        date: null,
      },
      ageGroup: {
        name: null,
        count: null,
        date: null,
      },
      timeSpent: {
        count: null,
        date: null,
      },
      blockListed: {
        count: null,
        date: null,
      },
      lowestFootfall: {
        count: null,
        date: null,
      },
    },
    payload: {
      report_type: "Date Wise Report",
      from_date: null,
      to_date: null,
      daily_date: null,
      UserID: "",
      department_ids: [],
      status: ``,
      age_category: ``,
      type: ``,
      Gender: ``,
      DeviceID: "",
      branch_id: "",
      include_device_types: ["all", "Access Control"],
    },

    response: "",
    data: [],
    errors: [],
    report_template: "Template1",
    headers: [
      {
        text: "Date",
        align: "left",
        sortable: true,
        key: "date",
        value: "date",
      },

      {
        text: "Total Footfall",
        align: "center",
        sortable: true,
        key: "total_footfall",
        value: "total_footfall",
      },
      {
        text: "In",
        align: "center",
        sortable: true,
        key: "in",
        value: "in",
      },
      {
        text: "Male",
        align: "center",
        sortable: true,
        key: "male",
        value: "male",
      },
      {
        text: "Female",
        align: "center",
        sortable: true,
        key: "female",
        value: "female",
      },
      {
        text: "Child",
        align: "center",
        sortable: true,
        key: "child",
        value: "child",
      },
      {
        text: "Out",
        align: "center",
        sortable: true,
        key: "out",
        value: "out",
      },
      {
        text: "Occupancy",
        align: "center",
        sortable: true,
        key: "occupancy",
        value: "occupancy",
      },

      {
        text: "Avg Time  ",
        align: "center",
        sortable: true,
        key: "avg_time",
        value: "avg_time",
      },
      {
        text: "Min Time",
        align: "center",
        sortable: true,
        key: "min_time",
        value: "min_time",
      },
      {
        text: "Max Time",
        align: "center",
        sortable: true,
        key: "max_time",
        value: "max_time",
      },

      {
        text: "VIP  ",
        align: "center",
        sortable: true,
        key: "vip",
        value: "vip",
      },
      {
        text: "Repeated",
        align: "center",
        sortable: true,
        key: "repeated",
        value: "repeated",
      },
      {
        text: "Blocked",
        align: "center",
        sortable: true,
        key: "blocked",
        value: "blocked",
      },
    ],
    max_date: null,

    isCompany: true,
    branches: [],
  }),

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  mounted() {
    this.tableHeight = window.innerHeight - 370;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 370;
    });
  },
  created() {
    let branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id", //sorting
        value: "branch.branch_name", //edit purpose

        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(0, 0, ...branch_header);
    this.setFromDate();
    this.getBranches();
    this.getScheduledEmployees();
    this.getDeviceList();
  },
  methods: {
    filterAttr(data) {
      this.payload.from_date = data.from;
      this.payload.to_date = data.to;
      //this.getDataFromApi();
    },
    getBranches() {
      if (this.$auth.user.branch_id) {
        this.payload.branch_id = this.$auth.user.branch_id;
        this.isCompany = false;
        return;
      }

      this.$axios
        .get("branch", {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.branches = data.data;
        });
    },

    getScheduledEmployees() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`/customer-list`, options).then(({ data }) => {
        this.employees = data;
      });
    },

    getDeviceList() {
      this.$axios
        .get(`/device_list`, {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.devices = data.filter((e) => !e.name.includes("Mobile"));
        });
    },

    setFromDate() {
      if (this.payload.from_date == null) {
        const dt = new Date();
        const y = dt.getFullYear();
        const m = dt.getMonth() + 1;
        const formattedMonth = m < 10 ? "0" + m : m;
        this.payload.from_date = `${y}-${formattedMonth}-01`;
      }
    },

    can(per) {
      return this.$pagePermission.can(per, this);
    },

    async getDataFromApi() {
      if (!this.payload.from_date) return false;

      if (this.payload.from_date) {
        this.payload.from_date = this.payload.from_date;
      }

      if (this.payload.to_date) {
        this.payload.to_date = this.payload.to_date;
      }
      this.loading = true;
      const { data, total } = await this.$store.dispatch("fetchData", {
        key: "access_control_report",
        options: this.options,
        refresh: true,
        endpoint: this.endpoint,
        filters: this.payload,
      });
      this.data = data;

      this.totalRowsCount = total;
      this.loading = false;

      this.getHighestValues(true);
      this.getHighestValues(false);

      if (this.data[0]) {
        if (this.data[0].highest_peak_hours_weekDays) {
          this.peakHoursListWeekDays =
            this.data[0].highest_peak_hours_weekDays.top3Highest;
          this.leanHoursListWeekDays =
            this.data[0].highest_peak_hours_weekDays.top3Lowest;
          this.peakHoursListWeekEnds =
            this.data[0].highest_peak_hours_weekEnds.top3Highest;
          this.leanHoursListWeekEnds =
            this.data[0].highest_peak_hours_weekEnds.top3Lowest;
        }
      }
    },
    getHighestValues(isWeekEnd) {
      let highestMaleCount = 0;
      let highestMaleCountDate = "---";
      let highestFemaleCount = 0;
      let highestFemaleCountDate = "---";
      let highestFootFall = 0;
      let highestFootFallDate = "---";
      let highestAgeGroupname = "---";
      let highestAgeGroup = 0;
      let highestAgeGroupDate = "---";

      let highestTimeSpent = "---";
      let highestTimeSpentDate = "---";

      let highestBlocklisted = "---";
      let highestBlocklistedDate = "---";

      let lowestFootfall = Infinity;
      let lowestFootfallDate = "---";

      let weekendsList = [];

      if (this.data[0]) {
        weekendsList = this.data[0].weekendsList;

        this.data.forEach((entry) => {
          let dayName = this.$dateFormat.getDayFullName(entry.date);
          if (isWeekEnd) {
            if (weekendsList[dayName] == 0) {
              return;
            }
          } else {
            //non weekends

            if (weekendsList[dayName] == 1) {
              return;
            }
          }
          if (entry.male_count > highestMaleCount) {
            highestMaleCount = entry.male_count;
            highestMaleCountDate = entry.date;
          }
          if (entry.female_count > highestFemaleCount) {
            highestFemaleCount = entry.female_count;
            highestFemaleCountDate = entry.date;
          }

          if (entry.out_count + entry.in_count > highestFootFall) {
            highestFootFall = entry.out_count + entry.in_count;
            highestFootFallDate = entry.date;
          }

          //AgeGroup
          if (entry.senior_count > highestAgeGroup) {
            highestAgeGroup = entry.senior_count;
            highestAgeGroupname = "Seniors";
            highestAgeGroupDate = entry.date;
          }
          if (entry.adult_count > highestAgeGroup) {
            highestAgeGroup = entry.adult_count;
            highestAgeGroupname = "Adults";
            highestAgeGroupDate = entry.date;
          }
          if (entry.younger_count > highestAgeGroup) {
            highestAgeGroup = entry.younger_count;
            highestAgeGroupname = "Youngers";
            highestAgeGroupDate = entry.date;
          }
          if (entry.child_count > highestAgeGroup) {
            highestAgeGroup = entry.child_count;
            highestAgeGroupname = "Kids";
            highestAgeGroupDate = entry.date;
          }

          if (entry.max_hrs > highestTimeSpent) {
            highestTimeSpent = entry.max_hrs;
            highestTimeSpentDate = entry.date;
          }

          if (entry.blocklisted_customer_count > highestBlocklisted) {
            highestBlocklisted = entry.blocklisted_customer_count;
            highestBlocklistedDate = entry.date;
          }

          if (entry.out_count + entry.in_count < lowestFootfall) {
            lowestFootfall = entry.out_count + entry.in_count;
            lowestFootfallDate = entry.date;
          }
        });
      }

      if (lowestFootfall == Infinity) {
        lowestFootfall = "---";
      }
      if (highestFootFall == lowestFootfall) {
        lowestFootfall = "---";
        lowestFootfallDate = "---";
      }
      if (highestFootFallDate == "---") {
        highestFootFall = "---";
      }
      let result = {
        footFall: {
          count: highestFootFall,
          date: highestFootFallDate,
        },
        maleCount: {
          count: highestMaleCount,
          date: highestMaleCountDate,
        },
        femaleCount: {
          count: highestFemaleCount,
          date: highestFemaleCountDate,
        },
        ageGroup: {
          name: highestAgeGroupname,
          count: highestAgeGroup,
          date: highestAgeGroupDate,
        },
        timeSpent: {
          count: highestTimeSpent,
          date: highestTimeSpentDate,
        },
        blockListed: {
          count: highestBlocklisted,
          date: highestBlocklistedDate,
        },
        lowestFootfall: {
          count: lowestFootfall,
          date: lowestFootfallDate,
        },
      };
      if (isWeekEnd) {
        this.highestCountsWeekend = result;
      } else {
        this.highestCounts = result;
      }

      console.log(this.highestCounts);
    },
    pdfDownload() {
      let path = process.env.BACKEND_URL + "/pdf";
      let pdf = document.createElement("a");
      pdf.setAttribute("href", path);
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },
    caps(str) {
      if (str) return str.replace(/\b\w/g, (c) => c.toUpperCase());
      else return "";
    },
    async process_file(type) {
      try {
        if (!this.data || !this.data.length) {
          alert("No data found");
          return;
        }

        const backendUrl = process.env.BACKEND_URL;
        const queryParams = {
          company_id: this.$auth.user.company_id,
          branch_id: this.payload.branch_id,
          UserID: this.payload.UserID,
          DeviceID: this.payload.DeviceID,
          from_date: this.payload.from_date,
          to_date: this.payload.to_date,
          report_type: this.payload.report_type,
          user_type: this.payload.user_type,
        };

        if (this.payload.from_date) {
          queryParams.from_date = this.payload.from_date;
        }

        if (this.payload.to_date) {
          queryParams.to_date = this.payload.to_date;
        }

        const queryString = Object.keys(queryParams)
          .map(
            (key) =>
              `${encodeURIComponent(key)}=${encodeURIComponent(
                queryParams[key]
              )}`
          )
          .join("&");

        const reportUrl = `${backendUrl}/${type}?${queryString}&include_device_types[]=all&include_device_types[]=Access Control`;

        const report = document.createElement("a");
        report.setAttribute("href", reportUrl);
        report.setAttribute("target", "_blank");
        report.click();
      } catch (error) {
        console.error("Error processing file:", error.message);
        // Handle the error (e.g., show an error message to the user)
      }
    },
  },
};
</script>
