<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dynamic Banking Form</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    >

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div id="app" v-cloak>

    <div class="container py-4">

        <!-- TOP STEPS -->

        <!-- <div class="step-bg p-4 mb-4">

            <div class="row text-center">

                <div class="col">
                    <div class="step-active">1</div>
                    <div class="mt-2">Personal</div>
                </div>

                <div class="col">
                    <div class="step-normal">2</div>
                    <div class="mt-2">Residence</div>
                </div>

                <div class="col">
                    <div class="step-normal">3</div>
                    <div class="mt-2">Employment</div>
                </div>

                <div class="col">
                    <div class="step-normal">4</div>
                    <div class="mt-2">Documents</div>
                </div>

            </div>

        </div> -->

        <div class="mb-3">

            <h2 class="fw-bold">
                Share More Details to Complete Application
            </h2>

            <p class="text-muted">
                <span class="text-danger">*</span>
                Mandatory Fields
            </p>

        </div>

        <!-- ACCORDION -->

        <div
            class="accordion"
            id="mainAccordion"
        >

            <div
                class="accordion-item shadow-sm mb-3 border-0 rounded-4 overflow-hidden"
                v-for="(fields, sectionKey) in bank_fields"
                :key="sectionKey">
            

                <h2 class="accordion-header">

                    <button
                        class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        :data-bs-target="'#'+sectionKey"
                    >

                        <div class="d-flex w-100 justify-content-between align-items-center">

                            <div>

                                <span class="section-dot"></span>

                                {{ formatTitle(sectionKey) }}

                            </div>

                            <span class="badge bg-primary">
                                Required
                            </span>

                        </div>

                    </button>

                </h2>

                <div
                    :id="sectionKey"
                    class="accordion-collapse collapse show"
                >

                    <div class="accordion-body">

                        <div class="row">

                            <!-- FIELDS -->

                            <div
                                class="col-md-6 mb-4"
                                v-for="field in flatFields(fields)"
                                :key="field.fieldKey"
                            >

                                <div class="field-card">

                                    <!-- LABEL -->

                                    <label class="form-label fw-semibold">

                                        {{ field.fieldName }}

                                        <span
                                            v-if="field.attributes.mandatory == 'y'"
                                            class="text-danger"
                                        >
                                            *
                                        </span>

                                    </label>

                                    <!-- TEXT -->

                                    <div
                                        v-if="field.fieldType == 'text'"
                                    >

                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="ds[field.fieldKey]"
                                            :placeholder="field.attributes.placeHolder"
                                        >

                                        <small
                                            class="text-danger"
                                            v-if="verror[field.fieldKey]"
                                        >
                                            {{ verror[field.fieldKey] }}
                                        </small>

                                    </div>

                                    <!-- DATE -->

                                    <div
                                        v-if="field.fieldType == 'calender'"
                                    >

                                        <div class="input-group">

                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="ds[field.fieldKey]"
                                            >

                                            <span class="input-group-text">
                                                <i class="bi bi-calendar3"></i>
                                            </span>

                                        </div>

                                    </div>

                                    <!-- DROPDOWN -->

                                    <div
                                        v-if="field.fieldType == 'dropdown'"
                                    >

                                        <select
                                            class="form-select"
                                            v-model="ds[field.fieldKey]"
                                        >

                                            <option value="">
                                                Select
                                            </option>

                                            <option
                                                v-for="option in field.attributes.values"
                                                :value="option.option"
                                            >
                                                {{ option.option }}
                                            </option>

                                        </select>

                                    </div>

                                    <!-- RADIO -->

                                    <div
                                        v-if="field.fieldType == 'radio'"
                                    >

                                        <div
                                            class="form-check form-check-inline"
                                            v-for="option in field.attributes.values"
                                        >

                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                :name="field.fieldKey"
                                                :value="option.option"
                                                v-model="ds[field.fieldKey]"
                                            >

                                            <label class="form-check-label">
                                                {{ option.option }}
                                            </label>

                                        </div>

                                    </div>

                                    <!-- FILE -->

                                    <div
                                        v-if="field.fieldType == 'file_upload'"
                                    >

                                        <input
                                            type="file"
                                            class="form-control"
                                            @change="handleFile($event, field.fieldKey)"
                                        >

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- BUTTON -->

        <div class="text-end mt-4">

            <button
                class="btn btn-primary px-5"
                @click="submitForm"
            >
                Continue
            </button>

        </div>

    </div>

</div>

<!-- Vue -->
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

new Vue({

    el : "#app",

    data : {

        bank_fields : {},

        ds : {},

        verror : {},

        files : {}

    },

    mounted() {

        this.loadForm();

    },

    methods : {

        loadForm() {

            axios.get("api/getForm.php")

            .then((response) => {

                this.bank_fields = response.data.details[0];

            });

        },

        formatTitle(title) {

            return title
                .replaceAll("_", " ")
                .toUpperCase();

        },

        flatFields(fields) {

            let output = [];

            fields.forEach(field => {

                output.push(field);

                if(
                    field.attributes &&
                    field.attributes.values
                ){

                    field.attributes.values.forEach(option => {

                        if(
                            this.ds[field.fieldKey] == option.option &&
                            option.child &&
                            option.child.length
                        ){

                            option.child.forEach(child => {

                                output.push(child);

                                // SUB CHILD

                                if(
                                    child.attributes &&
                                    child.attributes.values
                                ){

                                    child.attributes.values.forEach(sub => {

                                        if(
                                            this.ds[child.fieldKey] == sub.option &&
                                            sub.child &&
                                            sub.child.length
                                        ){

                                            sub.child.forEach(subchild => {

                                                output.push(subchild);

                                            });

                                        }

                                    });

                                }

                            });

                        }

                    });

                }

            });

            return output;

        },

        handleFile(event, key) {

            this.files[key] = event.target.files[0];

        },

        validate() {

            this.verror = {};

            Object.values(this.bank_fields)
            .forEach(section => {

                section.forEach(field => {

                    if(
                        field.attributes.mandatory == 'y'
                    ){

                        if(
                            !this.ds[field.fieldKey]
                        ){

                            this.verror[field.fieldKey] =
                                field.fieldName + " is required";

                        }

                    }

                });

            });

            return Object.keys(this.verror).length == 0;

        },

        submitForm() {

            if(!this.validate())
            {

                alert("Please fill all required fields");

                return;

            }

            let formData = new FormData();

            formData.append(
                "formData",
                JSON.stringify(this.ds)
            );

            for(let key in this.files)
            {

                formData.append(
                    key,
                    this.files[key]
                );

            }

            axios.post(
                "api/submit.php",
                formData
            )

            .then((response) => {

                alert(response.data.message);

                console.log(response.data);

            });

        }

    }

});

</script>

</body>
</html>