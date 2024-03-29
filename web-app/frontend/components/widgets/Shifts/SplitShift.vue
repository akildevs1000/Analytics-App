<template>
  <div>
    <span v-if="errors && errors.name" class="text-danger">{{
      errors.name[0]
    }}</span>
    <v-card class="mt-1" outlined>
      <v-container>
        <label dense>
          <b>Section 1</b>
        </label>
        <v-row>
          <v-col cols="12" md="2">
            <TimePickerCommon
              label="On Duty Time"
              :default_value="payload.on_duty_time"
              @getTime="(value) => (payload.on_duty_time = value)"
            />
            <span v-if="errors && errors.off_duty_time" class="text-danger">{{
              errors.off_duty_time[0]
            }}</span>
          </v-col>
          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Off Duty Time"
              :default_value="payload.off_duty_time"
              @getTime="(value) => (payload.off_duty_time = value)"
            />
            <span v-if="errors && errors.off_duty_time" class="text-danger">{{
              errors.off_duty_time[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Beginning In"
              :default_value="payload.beginning_in"
              @getTime="(value) => (payload.beginning_in = value)"
            />
            <span v-if="errors && errors.beginning_in" class="text-danger">{{
              errors.beginning_in[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Beginning Out"
              :default_value="payload.beginning_out"
              @getTime="(value) => (payload.beginning_out = value)"
            />
            <span v-if="errors && errors.beginning_out" class="text-danger">{{
              errors.beginning_out[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Ending In"
              :default_value="payload.ending_in"
              @getTime="(value) => (payload.ending_in = value)"
            />
            <span v-if="errors && errors.ending_in" class="text-danger">{{
              errors.ending_in[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Ending Out"
              :default_value="payload.ending_out"
              @getTime="(value) => (payload.ending_out = value)"
            />
            <span v-if="errors && errors.ending_out" class="text-danger">{{
              errors.ending_out[0]
            }}</span>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
    <br />
    <v-card outlined>
      <v-container>
        <label dense>
          <b>Section 2</b>
        </label>
        <v-row>
          <v-col cols="12" md="2">
            <TimePickerCommon
              label="On Duty Time"
              :default_value="payload.on_duty_time1"
              @getTime="(value) => (payload.on_duty_time1 = value)"
            />
            <span v-if="errors && errors.on_duty_time1" class="text-danger">{{
              errors.on_duty_time1[0]
            }}</span>
          </v-col>
          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Off Duty Time"
              :default_value="payload.off_duty_time1"
              @getTime="(value) => (payload.off_duty_time1 = value)"
            />
            <span v-if="errors && errors.off_duty_time1" class="text-danger">{{
              errors.off_duty_time1[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Beginning In"
              :default_value="payload.beginning_in1"
              @getTime="(value) => (payload.beginning_in1 = value)"
            />
            <span v-if="errors && errors.beginning_in1" class="text-danger">{{
              errors.beginning_in1[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Beginning Out"
              :default_value="payload.beginning_out1"
              @getTime="(value) => (payload.beginning_out1 = value)"
            />
            <span v-if="errors && errors.beginning_out1" class="text-danger">{{
              errors.beginning_out1[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Ending In"
              :default_value="payload.ending_in1"
              @getTime="(value) => (payload.ending_in1 = value)"
            />
            <span v-if="errors && errors.ending_in1" class="text-danger">{{
              errors.ending_in1[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Ending Out"
              :default_value="payload.ending_out1"
              @getTime="(value) => (payload.ending_out1 = value)"
            />
            <span v-if="errors && errors.ending_out1" class="text-danger">{{
              errors.ending_out1[0]
            }}</span>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
    <br />
    <v-card outlined>
      <v-container>
        <div><h6>Common Section</h6></div>
        <v-row>
          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Late Time"
              :default_value="payload.late_time"
              @getTime="(value) => (payload.late_time = value)"
            />
            <span v-if="errors && errors.late_time" class="text-danger">{{
              errors.late_time[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Early Time"
              :default_value="payload.early_time"
              @getTime="(value) => (payload.early_time = value)"
            />
            <span v-if="errors && errors.early_time" class="text-danger">{{
              errors.early_time[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Absent In"
              :default_value="payload.absent_min_in"
              @getTime="(value) => (payload.absent_min_in = value)"
            />
            <span v-if="errors && errors.absent_min_in" class="text-danger">{{
              errors.absent_min_in[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Absent Out"
              :default_value="payload.absent_min_out"
              @getTime="(value) => (payload.absent_min_out = value)"
            />
            <span v-if="errors && errors.absent_min_out" class="text-danger">{{
              errors.absent_min_out[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="Min working hrs"
              :default_value="payload.working_hours"
              @getTime="(value) => (payload.working_hours = value)"
            />
            <span v-if="errors && errors.working_hours" class="text-danger">{{
              errors.working_hours[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="2">
            <TimePickerCommon
              label="OT start after duty hrs"
              :default_value="payload.overtime_interval"
              @getTime="(value) => (payload.overtime_interval = value)"
            />
            <span
              v-if="errors && errors.overtime_interval"
              class="text-danger"
              >{{ errors.overtime_interval[0] }}</span
            >
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12">
            <DaysPickerCommon
              label="Working Days"
              :default_value="payload.days"
              @selectedDays="(value) => (payload.days = value)"
            />
            <span v-if="errors && errors.days" class="text-danger">{{
              errors.days[0]
            }}</span>
          </v-col>

          <v-col cols="12" md="4">
      <v-select
        label="Half Day"
        v-model="payload.halfday"
        :items="[
          { id: `Not Applicable`, name: `Not Applicable` },
          { id: `Monday`, name: `Monday` },
          { id: `Tuesday`, name: `Tuesday` },
          { id: `Wednesday`, name: `Wednesday` },
          { id: `Thursday`, name: `Thursday` },
          { id: `Friday`, name: `Friday` },
          { id: `Saturday`, name: `Saturday` },
          { id: `Sunday`, name: `Sunday` },
        ]"
        item-value="id"
        item-text="name"
        dense
        outlined
        :hide-details="true"
      ></v-select>
      <span v-if="errors && errors.halfday" class="text-danger">{{
        errors.halfday[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="4">
      <TimePickerCommon
        label="Number of working hours"
        :default_value="payload.halfday_working_hours"
        @getTime="
          (value) => {
            payload.halfday_working_hours = value;
          }
        "
      />
      <span v-if="errors && errors.off_duty_time" class="text-danger">{{
        errors.off_duty_time[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="4"> </v-col>

          <v-col cols="12" md="4">
            <WeekendPickerCommon
              label="Weekend 1"
              :default_value="payload.weekend1"
              @selectedWeekend="(value) => (payload.weekend1 = value)"
            />
            <span v-if="errors && errors.weekend1" class="text-danger">{{
              errors.weekend1[0]
            }}</span>
          </v-col>
          <v-col cols="12" md="4">
            <WeekendPickerCommon
              label="Weekend 2"
              :default_value="payload.weekend2"
              @selectedWeekend="(value) => (payload.weekend2 = value)"
            />
            <span v-if="errors && errors.weekend2" class="text-danger">{{
              errors.weekend2[0]
            }}</span>
          </v-col>
          <v-col cols="12" md="4">
            <MonthlyFlexiHolidays
              label="Monthly Flexible Holidays"
              :default_value="payload.monthly_flexi_holidays"
              @selectedMonthlyHolidays="
                (value) => (payload.monthly_flexi_holidays = value)
              "
            />
            <span
              v-if="errors && errors.monthly_flexi_holidays"
              class="text-danger"
              >{{ errors.monthly_flexi_holidays[0] }}</span
            >
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="6">
            <DatePickerCommon
              label="From Date"
              :default_value="currentDate"
              @selectedDate="(value) => (payload.from_date = value)"
            />
            <span v-if="errors && errors.from_date" class="text-danger">{{
              errors.from_date[0]
            }}</span>
          </v-col>
          <v-col cols="12" md="6">
            <DatePickerCommon
              label="To Date"
              :default_value="nextYearDate"
              @selectedDate="(value) => (payload.to_date = value)"
            />
            <span v-if="errors && errors.to_date" class="text-danger">{{
              errors.to_date[0]
            }}</span>
          </v-col>
          <v-col cols="12">
            <v-btn
              v-if="payload && payload.id > 0"
              small
              color="primary"
              @click="update"
            >
              Update
            </v-btn>
            <v-btn v-else small color="primary" @click="submit"> Submit </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </div>
</template>

<script>
import TimePickerCommon from "../../Snippets/TimePickerCommon.vue";
import DaysPickerCommon from "../../Snippets/DaysPickerCommon.vue";
import WeekendPickerCommon from "../../Snippets/WeekendPickerCommon.vue";
import MonthlyFlexiHolidays from "../../Snippets/MonthlyFlexiHolidays.vue";
import DatePickerCommon from "../../Snippets/DatePickerCommon.vue";

const currentDate = new Date();
const nextYearDate = new Date(currentDate);
nextYearDate.setFullYear(currentDate.getFullYear() + 1);

export default {
  props: ["shift_type_id", "name", "shift_id", "payload"],
  components: {
    TimePickerCommon,
    DaysPickerCommon,
    WeekendPickerCommon,
    MonthlyFlexiHolidays,
    DatePickerCommon,
  },
  watch: {
    name(value) {
      this.payload.name = value;
    },
  },
  data: () => ({
    currentDate,
    nextYearDate,
    errors: [],
  }),
  created() {
    this.$axios
      .get(`/shift/${this.shift_id}`)
      .then(({ data }) => {
        // this.payload = data;
        console.log(data);
      })
      .catch(({ message }) => {
        this.snackbar = true;
        this.response = message;
        this.showDialog = false;
      });
  },
  methods: {
    submit() {
      this.payload.company_id = this.$auth.user.company_id;

      this.payload.shift_type_id = this.shift_type_id;
      if (!this.payload.from_date) {
        this.payload.from_date = this.currentDate;
      }
      if (!this.payload.to_date) {
        this.payload.to_date = this.nextYearDate;
      }

      this.loading = true;
      this.$axios
        .post(`/shift`, this.payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Shift added successfully";
            this.$emit("success");
            this.$emit("close-popup");
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
          this.showDialog = false;
        });
    },
    update() {
      this.payload.company_id = this.$auth.user.company_id;

      this.payload.shift_type_id = this.shift_type_id;
      this.loading = true;

      this.$axios
        .put(`/shift/${this.payload.id}`, this.payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Shift update successfully";
            this.$emit("success");
            this.$emit("close-popup");
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
  },
};
</script>
