console.log("js started");

window.onload = function () {
  console.log("window loaded");

  const appEl = document.getElementById("vue-app");
  if (!appEl) return;

  window.hssVueTemplateApp = new Vue({
    el: "#vue-app",
    vuetify: new Vuetify(),
    data() {
      return {
        message: "Hello from Vue + Vuetify!",
        ajaxResponse: "",
        loading: false, // Load flag during any async operation
        form: {
          field1: '',
        },
        rules: {
          required: v => !!v || 'This field is required', // flag for sample input field
        },
      };
    },
    methods: {
      submitForm() {
        const isValid = this.$refs.form.validate();
        if (!isValid) return;

        this.loading = true; // Set true during ajax calls

        fetch(HSS_VUE_AJAX.ajax_url, { 
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "hss_vue_sample_ajax",
            security: HSS_VUE_AJAX.nonce,
            type: "submit",
            field1: this.form.field1,
          }),
        })
          .then((res) => res.json())
          .then((data) => {
            this.ajaxResponse = data.success ? data.data.message : data.data.message || "Something went wrong.";
          })
          .catch((err) => {
            console.error("AJAX error:", err);
            this.ajaxResponse = "Server error. Try again.";
          })
          .finally(() => {
            this.loading = false; // set off after request
          });
      }
    },
    mounted() {
      this.loading = true;

      fetch(HSS_VUE_AJAX.ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "hss_vue_sample_ajax",
          security: HSS_VUE_AJAX.nonce,
          type: "init"
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          console.log("AJAX response:", data);
        })
        .catch((err) => {
          console.error("AJAX error:", err);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  });
};
