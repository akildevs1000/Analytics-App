<template>
  <v-dialog v-model="dialog" width="1000">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <v-icon color="black" small>mdi-eye</v-icon>
        View
      </span>
    </template>
    <v-stepper v-model="step" horizontal>
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1" editable>
          Basic Info
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 2" step="2" editable>
          Vehicle Info
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 3" step="3" editable>
          Documentation
        </v-stepper-step>
      </v-stepper-header>

      <v-stepper-content step="1">
        <!-- Step 1 Content -->
        <v-row>
          <v-col cols="4" style="borders: 1px solid #dddddd">
            <v-row>
              <!-- <v-col cols="12">
                    <v-toolbar dense flat>
                      <b><small>Primary Members</small></b>
                    </v-toolbar>
                  </v-col> -->
              <v-col cols="12" class="text-center">
                <v-avatar size="150" style="border: 1px solid #6946dd">
                  <v-img
                    :src="item.profile_picture || `/no-profile-image.jpg`"
                  ></v-img>
                </v-avatar>
                <div class="mt-1">
                  {{ item.first_name }} {{ item.last_name }}
                </div>
                <small>
                  {{ item.nationality }} <br />

                  {{ item.phone_number }}
                </small>
              </v-col>
              <v-col cols="12">
                <v-divider></v-divider>
                <v-row no-gutters>
                  <v-col cols="6"> <small>Date of Birth</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.date_of_birth }} </small>
                  </v-col>
                  <v-col cols="6"> <small>Whatsapp</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.whatsapp_number }} </small>
                  </v-col>
                  <v-col cols="6"> <small>Address</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.address }} </small>
                  </v-col>

                  <v-col cols="6"> <small>Floor Number</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.floor_id }} </small>
                  </v-col>

                  <v-col cols="6"> <small>Room Number</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.floor_id }} </small>
                  </v-col>

                  <v-col cols="6"> <small>Car Number</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.car_number }} </small>
                  </v-col>

                  <v-col cols="6"> <small>PIN</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.pin }} </small>
                  </v-col>

                  <v-col cols="6"> <small>Start Date</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.start_date }} </small>
                  </v-col>

                  <v-col cols="6"> <small>End Date</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.end_date }} </small>
                  </v-col>

                  <v-col cols="6"> <small>Web Access</small> </v-col>
                  <v-col cols="6" class="text-right">
                    <small> {{ item.web_access ? "Yes" : "No" }}</small>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="8" class="align-center">
            <v-alert
              outlined
              color="primary"
              dense
              class="text-center"
              v-if="!item.members.length"
            >
              No member added yet
            </v-alert>
            <TanentMembers v-else :members="item.members" />
          </v-col>
        </v-row>
      </v-stepper-content>

      <v-stepper-content step="2">
        <v-container>
          <v-alert
            v-if="!item.vehicles.length"
            outlined
            color="primary"
            dense
            class="text-center"
          >
            No Vehicle info found
          </v-alert>
          <table v-else>
            <tr>
              <td class="text-h6">Car Number</td>
              <td class="text-h6">Parking Number</td>
            </tr>
            <tr v-for="(vehicles, index) in item.vehicles" :key="index">
              <td>{{ vehicles.car_number }}</td>
              <td>{{ vehicles?.parking?.parking_number }}</td>
            </tr>
          </table>
        </v-container>
      </v-stepper-content>

      <v-stepper-content step="3">
        <v-container>
          <v-row>
            <v-col cols="6">
              <ViewAttachment :src="item.passport_doc" label="Passport" />
            </v-col>
            <v-col cols="6">
              <ViewAttachment :src="item.id_doc" label="ID" />
            </v-col>
            <v-col cols="6">
              <ViewAttachment :src="item.contract_doc" label="Contract" />
            </v-col>
            <v-col cols="6">
              <ViewAttachment :src="item.ejari_doc" label="Ejari" />
            </v-col>
            <v-col cols="6">
              <ViewAttachment :src="item.license_doc" label="License" />
            </v-col>
            <v-col cols="6">
              <ViewAttachment :src="item.others_doc" label="Others" />
            </v-col>
          </v-row>
        </v-container>
      </v-stepper-content>
    </v-stepper>
  </v-dialog>
</template>
<script>
export default {
  props: ["item"],
  data() {
    return {
      step: 1,
      dialog: false,
    };
  },
};
</script>

<style scoped>
@import url("../../assets/tableStyles.css");
</style>
