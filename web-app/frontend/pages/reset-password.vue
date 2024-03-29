<template>
  <v-app>
    <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
      {{ response }}
    </v-snackbar>
    <section class="h-100 gradient-form" style="background-color: #eee">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-xl-8">
            <div class="card1 rounded-3 text-black">
              <div class="row g-0">
                <div class="col-lg-6" style="background-color: #6946dd">
                  <div class="text-center" style="height: 100px; color: #fff">
                    <!-- <img width="35%" :src="logo" alt="logo" /> -->
                  </div>
                  <div class="card-body p-md-5 mx-md-4" style="color: #fff">
                    <h2 class="pb-7">Welcome To MyTime2Cloud</h2>
                    <h4 class="mt-2 mb-5 pb-1">Reset Password</h4>
                    <v-form v-if="emailView" ref="form" method="post">
                      <div class="form-outline">
                        <v-text-field
                          dark
                          color="white--text"
                          rounded
                          v-model="email"
                          :hide-details="false"
                          id="form2Example11"
                          placeholder="Email"
                          autofill="false"
                          required
                          dense
                          outlined
                          type="email"
                          prepend-inner-icon="mdi-email"
                          autocomplete="false"
                          aria-autocomplete="none"
                        ></v-text-field>
                      </div>
                      <!-- <div class="form-outline mb-4">
                        <input v-model="email" style="border: 1px solid" type="email" id="form2Example11"
                          class="form-control" placeholder="master@erp.com" />
                      </div> -->

                      <div class="text-center pt-1 mb-5 pb-1">
                        <span v-if="errors && errors.email" class="error--text"
                          >{{ errors.email[0] }}
                        </span>
                        <span v-if="msg" class="error--text">{{ msg }} </span>

                        <v-btn
                          :loading="loading"
                          @click="reset_password"
                          class="btn btn-black btn-block white mt-1 mb-3 p-4 btntext"
                        >
                          Reset the Password
                        </v-btn>
                        <!-- <v-btn :loading="loading" @click="reset_password" class="
                            btn btn-primary btn-block
                            text-white
                            fa-lg
                            primary
                            mt-1
                            mb-3
                          ">
                          Submit
                        </v-btn> -->
                      </div>
                      <div class="text-right">
                        <nuxt-link class="text-muted text-right" to="/login"
                          >login</nuxt-link
                        >
                      </div>

                      <div
                        class="d-flex align-items-center justify-content-center pb-4"
                      ></div>
                    </v-form>

                    <v-form v-if="codeView" ref="form" method="post">
                      <!-- <p>Please enter your code</p> -->
                      <label for="">Code</label>
                      <div class="form-outline mb-4">
                        <input
                          v-model="code"
                          style="border: 1px solid"
                          type="number"
                          id="form2Example11"
                          class="form-control"
                          placeholder="master@erp.com"
                        />
                        <!-- <v-otp-input length="6"></v-otp-input> -->
                      </div>

                      <div class="text-center pt-1 mb-5 pb-1">
                        <span v-if="errors && errors.code" class="error--text"
                          >{{ errors.code[0] }}
                        </span>
                        <span v-if="msg" class="error--text">{{ msg }} </span>
                        <v-btn
                          :loading="loading"
                          @click="check_code"
                          class="btn btn-primary btn-block text-white fa-lg primary mt-1 mb-3"
                        >
                          Submit
                        </v-btn>
                      </div>

                      <div
                        class="d-flex align-items-center justify-content-center pb-4"
                      >
                        <p @click="reset_password" style="cursor: pointer">
                          Resend code
                        </p>
                      </div>
                    </v-form>

                    <v-form v-if="newPasswordView" ref="form" method="post">
                      <div class="form-outline mb-4">
                        <label class="col-form-label"
                          >New Password
                          <span class="text-danger">*</span></label
                        >
                        <v-text-field
                          dense
                          outlined
                          :append-icon="
                            show_password ? 'mdi-eye' : 'mdi-eye-off'
                          "
                          :type="show_password ? 'text' : 'password'"
                          v-model="password"
                          class="input-group--focused"
                          @click:append="show_password = !show_password"
                          :error="errors.password"
                          :error-messages="
                            errors && errors.password ? errors.password[0] : ''
                          "
                        ></v-text-field>
                        <label class="col-form-label"
                          >Confirm Password
                          <span class="text-danger">*</span></label
                        >
                        <v-text-field
                          dense
                          outlined
                          :append-icon="
                            show_password_confirm ? 'mdi-eye' : 'mdi-eye-off'
                          "
                          :type="show_password_confirm ? 'text' : 'password'"
                          v-model="password_confirmation"
                          class="input-group--focused"
                          @click:append="
                            show_password_confirm = !show_password_confirm
                          "
                          :error="errors.show_password_confirm"
                          :error-messages="
                            errors && errors.show_password_confirm
                              ? errors.show_password_confirm[0]
                              : ''
                          "
                        ></v-text-field>
                      </div>

                      <div class="text-center pt-1 mb-5 pb-1">
                        <span v-if="errors && errors.code" class="error--text"
                          >{{ errors.code[0] }}
                        </span>
                        <span v-if="msg" class="error--text">{{ msg }} </span>
                        <v-btn
                          :loading="loading"
                          @click="change_new_password"
                          class="btn btn-primary btn-block text-white fa-lg primary mt-1 mb-3"
                        >
                          Submit
                        </v-btn>
                      </div>

                      <div
                        class="d-flex align-items-center justify-content-center pb-4"
                      ></div>
                    </v-form>
                  </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center bgimage"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </v-app>
</template>

<script>
export default {
  layout: "login",
  auth: false,
  data: () => ({
    logo: "/ideaHRMS-final-blue.svg",

    loading: false,
    snackbar: false,
    response: "",
    show_password: false,
    show_password_confirm: false,
    emailView: true,
    codeView: false,
    newPasswordView: false,
    go_login_msg: false,

    email: "",
    code: "",
    password: "",
    password_confirmation: "",

    msg: "",
    errors: [],
  }),
  methods: {
    reset_password() {
      let payload = {
        email: this.email,
      };
      this.loading = true;

      this.$axios
        .post("/reset-password", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.msg = data.message;
          } else {
            this.snackbar = true;
            this.response = data.message;
            this.emailView = false;
            this.codeView = true;
          }
        })
        .catch((e) => console.log(e));
    },

    check_code() {
      let payload = {
        code: this.code,
        email: this.email,
      };
      this.loading = true;

      this.$axios
        .post("/check-code", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.msg = data.message;
          } else {
            this.msg = null;
            this.newPasswordView = true;
            this.emailView = false;
            this.codeView = false;
          }
        })
        .catch((e) => console.log(e));
    },

    change_new_password() {
      let payload = {
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation,
      };
      this.loading = true;

      this.$axios
        .post("/new-password", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.msg = data.message;
          } else {
            this.snackbar = true;
            this.response = data.message;
            this.$router.push(`/login`);
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
<style scoped>
.v-text-field--outlined >>> fieldset {
  border-color: #fff;
}

.v-list__group__header__prepend-icon .v-icon {
  color: red;
}

.v-input__icon {
  color: #fff !important;
}

.hidden-sm-and-down .v-icon {
  color: white !important;
}

.v-text-field--rounded {
  border-radius: 10px;
}

.text-white {
  color: #fff;
}

.v-label {
  color: #fff !important;
}

.bgimage {
  /* background-image: url(../static/login2.jpg) no-repeat center center fixed;
  ; */

  background-image: url("../static/login2.jpg");
  background-size: cover;

  min-height: 600px;
}

.v-btn {
  text-transform: inherit !important;
}

.v-input__control .v-label {
  color: red;
}

.btntext {
  color: #6946dd;
  font-weight: bold;
  font-size: 22px;
}

@media (min-width: 768px) {
  .gradient-form {
    height: 100vh !important;
  }
}

@media (min-width: 769px) {
  .primary {
    border-top-right-radius: 0.3rem;
    border-bottom-right-radius: 0.3rem;
  }
}
</style>
