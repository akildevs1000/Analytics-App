<template>
  <v-row>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="On Duty Time"
        :default_value="payload.on_duty_time"
        @getTime="(value) => (payload.on_duty_time = value)"
      />
      <span v-if="errors && errors.off_duty_time" class="text-danger">{{
        errors.off_duty_time[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Off Duty Time"
        :default_value="payload.off_duty_time"
        @getTime="(value) => (payload.off_duty_time = value)"
      />
      <span v-if="errors && errors.off_duty_time" class="text-danger">{{
        errors.off_duty_time[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Min working hrs"
        :default_value="payload.working_hours"
        @getTime="(value) => (payload.working_hours = value)"
      />
      <span v-if="errors && errors.working_hours" class="text-danger">{{
        errors.working_hours[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="OT start after duty hrs"
        :default_value="payload.overtime_interval"
        @getTime="(value) => (payload.overtime_interval = value)"
      />
      <span v-if="errors && errors.overtime_interval" class="text-danger">{{
        errors.overtime_interval[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Beginning In"
        :default_value="payload.beginning_in"
        @getTime="(value) => (payload.beginning_in = value)"
      />
      <span v-if="errors && errors.beginning_in" class="text-danger">{{
        errors.beginning_in[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Beginning Out"
        :default_value="payload.beginning_out"
        @getTime="(value) => (payload.beginning_out = value)"
      />
      <span v-if="errors && errors.beginning_out" class="text-danger">{{
        errors.beginning_out[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Ending In"
        :default_value="payload.ending_in"
        @getTime="(value) => (payload.ending_in = value)"
      />
      <span v-if="errors && errors.ending_in" class="text-danger">{{
        errors.ending_in[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Ending Out"
        :default_value="payload.ending_out"
        @getTime="(value) => (payload.ending_out = value)"
      />
      <span v-if="errors && errors.ending_out" class="text-danger">{{
        errors.ending_out[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Late"
        :default_value="payload.late_time"
        @getTime="(value) => (payload.late_time = value)"
      />
      <span v-if="errors && errors.late_time" class="text-danger">{{
        errors.late_time[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Early"
        :default_value="payload.early_time"
        @getTime="(value) => (payload.early_time = value)"
      />
      <span v-if="errors && errors.early_time" class="text-danger">{{
        errors.early_time[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Absent For In"
        :default_value="payload.absent_min_in"
        @getTime="(value) => (payload.absent_min_in = value)"
      />
      <span v-if="errors && errors.absent_min_in" class="text-danger">{{
        errors.absent_min_in[0]
      }}</span>
    </v-col>
    <v-col cols="12" md="3">
      <TimePickerCommon
        label="Absent For Out"
        :default_value="payload.absent_min_out"
        @getTime="(value) => (payload.absent_min_out = value)"
      />
      <span v-if="errors && errors.absent_min_out" class="text-danger">{{
        errors.absent_min_out[0]
      }}</span>
    </v-col>
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
</template>

<script>
import TimePickerCommon from "../../Snippets/TimePickerCommon.vue";
import DaysPickerCommon from "../../Snippets/DaysPickerCommon.vue";
import WeekendPickerCommon from "../../Snippets/WeekendPickerCommon.vue";
import MonthlyFlexiHolidays from "../../Snippets/MonthlyFlexiHolidays.vue";

export default {
  props: ["payload", "errors"],
  components: {
    TimePickerCommon,
    DaysPickerCommon,
    WeekendPickerCommon,
    MonthlyFlexiHolidays,
  },
};
</script>
