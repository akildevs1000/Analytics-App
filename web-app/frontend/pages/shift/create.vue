<template>
  <div v-if="can(`shift_create`)">
    <!-- <GenerateLog /> -->
    <Back class="primary white--text" />

    <v-card class="mt-2">
      <!-- <v-toolbar flat dark class="primary"> Create {{ Model }} </v-toolbar> -->

      <v-stepper v-model="e1">
        <v-stepper-header>
          <v-stepper-step :complete="e1 > 1" step="1">
            Shift Type
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step :complete="e1 > 2" step="2">
            Shift Details
          </v-stepper-step>
        </v-stepper-header>

        <v-stepper-items>
          <v-stepper-content step="1">
            <v-card flat>
              <v-card-text>
                <v-row class="mx-auto">
                  <v-col cols="12">
                    Select Shift Type <span class="error--text">*</span>
                    <v-skeleton-loader
                      v-if="shift_types && !shift_types.length"
                      type="card"
                    />
                    <v-radio-group v-model="shift_type_id" row>
                      <v-radio
                        v-for="(shift_type, index) in shift_types"
                        :key="index"
                        :label="`${shift_type.name}`"
                        :value="shift_type.id"
                      ></v-radio>
                    </v-radio-group>
                  </v-col>
                  <v-col cols="12">
                    <div class="text-left">
                      <v-btn
                        dark
                        small
                        color="primary"
                        @click="(e1 = 2), getComponent()"
                      >
                        Continue
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-stepper-content>

          <v-stepper-content step="2">
            <v-card flat>
              <v-btn x-small dark fab color="background" @click="e1 = 1">
                <v-icon>mdi-arrow-left</v-icon>
              </v-btn>
              <v-card-text>
                <v-row>
                  <v-col cols="12">
                    <component :shift_type_id="shift_type_id" :is="comp" />
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-stepper-content>
        </v-stepper-items>
      </v-stepper>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
import Back from "../../components/Snippets/Back.vue";

export default {
  components: { Back },
  data: () => ({
    Model: "Shift",
    comp: null,
    shift_types: [],
    shift_last_id: "",
    shiftList: true,
    isChange: false,
    isAuto: false,

    week_days: [
      { label: "Sun", value: "Sun" },
      { label: "Mon", value: "Mon" },
      { label: "Tue", value: "Tue" },
      { label: "Wed", value: "Wed" },
      { label: "Thu", value: "Thu" },
      { label: "Fri", value: "Fri" },
      { label: "Sat", value: "Sat" },
    ],
    errors: [],
    data: [],
    response: "",
    snackbar: false,

    Model: "Shift",
    e1: 1,
    e6: 1,

    loading: false,
    time_in_menu: false,
    time_out_menu: false,
    grace_time_in_menu: false,
    grace_time_out_menu: false,

    beginning_in_menu: false,
    ending_in_menu: false,

    beginning_out_menu: false,
    ending_out_menu: false,

    shift_type_id: "",
    payload: {
      shift_type_id: 6,
      days: [],
    },

    errors: [],
    shifts: [],
    data: [],
    response: "",
    snackbar: false,
  }),

  created() {
    let options = {
      per_page: 1000,
      company_id: this.$auth.user.company_id,
    };

    this.$axios.get("shift_type", { params: options }).then(({ data }) => {
      this.shift_types = data;
    });
  },
  watch: {},
  computed: {},
  methods: {
    getComponent() {
      switch (this.shift_type_id) {
        case 6:
          this.comp = "SingleShift";
          break;
        case 5:
          this.comp = "widgetsShiftsSplitShift";
          break;
        case 4:
          this.comp = "OverNightShift";
          break;
        case 3:
          this.comp = "AutoShift";
          break;
        case 2:
          this.comp = "MultiInOutShift";
          break;
        default:
          this.comp = "FiloShift";
          break;
      }
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    getShifts() {
      let payload = {
        params: {
          shift_type_id: 6,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios
        .get("shift_by_type", payload)
        .then(({ data }) => {
          this.shifts = data;
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>
