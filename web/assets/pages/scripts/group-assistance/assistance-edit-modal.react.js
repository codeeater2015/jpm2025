var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var AssistanceEditModal = React.createClass({

    getInitialState: function () {
        return {
            showNewProfileModal: false,
            form: {
                data: {
                    finalBill: 0,
                    amount: 0,
                    hospital: "",
                    dependentDiagnosis: "",
                    transDate: "",
                    transType: "",
                    remarks: "",
                    clientProfileId: null,
                    dependentProfileId: null
                },
                errors: []
            }
        };
    },

    render: function () {
        var self = this;
        var data = this.state.form.data;

        return (
            <Modal style={{ marginTop: "10px" }} bsSize="lg" keyboard={false} enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>Edit Form</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                    {
                        this.state.showNewProfileModal &&
                        <AssistanceNewProfileModal
                            proId={3}
                            electId={4}
                            provinceCode={53}
                            show={this.state.showNewProfileModal}
                            onHide={this.closeNewProfileModal}
                        />
                    }

                    <form id="assist-edit-form" >
                        <div className="row">
                            <div className="col-md-12">
                                <div className="row">
                                    <div className="col-md-3">
                                        <FormGroup controlId="formTransDate" validationState={this.getValidationState('transDate')}>
                                            <ControlLabel> Date : </ControlLabel>
                                            <input type="date" value={this.state.form.data.transDate} className="input-sm form-control" onChange={this.setFormProp} name="transDate" />
                                            <HelpBlock>{this.getError('transDate')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                    <div className="col-md-3">
                                        <FormGroup controlId="formTransDate" validationState={this.getValidationState('controlNo')}>
                                            <ControlLabel> Control No : </ControlLabel>
                                            <input type="text" value={this.state.form.data.controlNo} className="input-sm form-control" onChange={this.setFormProp} name="controlNo" />
                                            <HelpBlock>{this.getError('controlNo')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-10">
                                        <FormGroup controlId="formClientProfileId" validationState={this.getValidationState('clientProfileId')}>
                                            <ControlLabel > Client Name : </ControlLabel>
                                            <select id="voter-select2" className="form-control input-sm">
                                            </select>
                                            <HelpBlock>{this.getError('clientProfileId')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                    <div className="col-md-2">
                                        <button style={{ marginTop: "25px" }} onClick={self.openNewProfileModal} type="button" className="btn btn-sm btn-primary">New Profile</button>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-10">
                                        <FormGroup controlId="formDependentProfileId" validationState={this.getValidationState('dependentProfileId')}>
                                            <ControlLabel > Patient Name : </ControlLabel>
                                            <select id="beneficiary-select2" className="form-control input-sm">
                                            </select>
                                            <HelpBlock>{this.getError('dependentProfileId')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                    <div className="col-md-2">
                                        <button style={{ marginTop: "25px" }} onClick={self.openNewProfileModal} type="button" className="btn btn-sm btn-primary">New Profile</button>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-6">
                                        <FormGroup controlId="formHospitalName" validationState={this.getValidationState('hospital')}>
                                            <ControlLabel> Hospital : </ControlLabel>
                                            <select id="hospital_select2" className="form-control form-filter input-sm" name="hospital">
                                            </select>
                                            <HelpBlock>{this.getError('hospital')}</HelpBlock>
                                        </FormGroup>
                                    </div>

                                    <div className="col-md-3">
                                        <FormGroup controlId="formTransType" validationState={this.getValidationState('transType')}>
                                            <ControlLabel> Type of Assistance : </ControlLabel>
                                            <select id="type_of_assistance_select2" className="form-control form-filter input-sm" name="transType">
                                            </select>
                                            <HelpBlock>{this.getError('transType')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-6">
                                        <FormGroup controlId="formDependentDiagnosis" validationState={this.getValidationState('dependentDiagnosis')}>
                                            <ControlLabel> Diagnosis : </ControlLabel>
                                            <select id="diagnosis_select2" className="form-control form-filter input-sm" name="dependentDiagnosis">
                                            </select>
                                            <HelpBlock>{this.getError('dependentDiagnosis')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-3">
                                        <FormGroup controlId="formFinalBill" validationState={this.getValidationState('finalBill')}>
                                            <ControlLabel> Final Bill : </ControlLabel>
                                            <FormControl bsClass="form-control input-sm" type="number" step="any" name="finalBill" value={this.state.form.data.finalBill} onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('finalBill')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                    <div className="col-md-3">
                                        <FormGroup controlId="formAmount" validationState={this.getValidationState('amount')}>
                                            <ControlLabel> Granted Amount : </ControlLabel>
                                            <FormControl bsClass="form-control input-sm" type="number" step="any" name="amount" value={this.state.form.data.amount} onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('amount')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <FormGroup controlId="formRemarks" validationState={this.getValidationState('remarks')}>
                                            <ControlLabel> Remarks : </ControlLabel>
                                            <FormControl componentClass="textarea" rows="6" bsClass="form-control input-sm" name="remarks" value={this.state.form.data.remarks} onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('remarks')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="clearfix" />

                        <div className="text-right" >
                            <button type="button" className="btn btn-default" style={{ marginRight: "5px" }} onClick={this.props.onHide}>Close</button>
                            <button type="button" className="btn btn-primary" onClick={this.submit}>Submit</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        );
    },

    componentDidMount: function () {
        this.initSelect2();
        this.loadData(this.props.id);
    },

    loadData: function (id) {
        var self = this;

        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_assistance_header", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;

            form.data.controlNo = res.controlNo;
            form.data.transDate = res.transDate;
            form.data.transType = res.transType;
            form.data.hospital = res.hospital;
            form.data.finalBill = res.finalBill;
            form.data.amount = res.amount;
            form.data.remarks = res.remarks;

            $("#assist-edit-form #type_of_assistance_select2").empty()
            .append($("<option/>")
                .val(res.transType)
                .text(res.transType))
            .trigger("change");

            $("#assist-edit-form #hospital_select2").empty()
            .append($("<option/>")
                .val(res.hospital)
                .text(res.hospital))
            .trigger("change");

            $("#assist-edit-form #diagnosis_select2").empty()
            .append($("<option/>")
                .val(res.dependentDiagnosis)
                .text(res.dependentDiagnosis))
            .trigger("change");


            var clientText = res.clientName + " | " +  res.purok + " " + res.barangayName + ", " + res.municipalityName;

            $("#assist-edit-form #voter-select2").empty()
            .append($("<option/>")
                .val(res.clientProfileId)
                .text(clientText))
            .trigger("change");

            var dependentText = res.dependentName + " | " +  res.dependentAddress;
            
            $("#assist-edit-form #beneficiary-select2").empty()
            .append($("<option/>")
                .val(res.dependentProfileId)
                .text(dependentText))
            .trigger("change");

            self.setState({ form: form });
        });
    },


    initSelect2: function () {
        var self = this;


        $("#assist-edit-form #voter-select2").select2({
            casesentitive: false,
            placeholder: "Select Applicant Name",
            allowClear: true,
            width: '100%',
            containerCssClass: ':all:',
            tags: true,
            disabled : true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
            ajax: {
                url: Routing.generate('ajax_select2_assistance_profiles'),
                data: function (params) {
                    return {
                        searchText: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            var text = item.fullname + ' ( ' + item.municipality_name + ',' + item.barangay_name + ' | ' + item.purok + ' | ' + item.contact_no;

                            return { id: item.id, text: text };
                        })
                    };
                },
            }
        });

        $("#assist-edit-form #beneficiary-select2").select2({
            casesentitive: false,
            placeholder: "Enter Name...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            disabled : true, 
            containerCssClass: ':all:',
            dropdownCssClass: 'custom-option',
            ajax: {
                url: Routing.generate('ajax_select2_assistance_profiles'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        electId: 4,
                        proId: 3,
                        provinceCode: 53,
                        municipalityNo: $("#assist-edit-form #municipality_select2").val()
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            var text = item.fullname + ' ( ' + item.municipality_name + ',' + item.barangay_name + ' | ' + item.purok + ' | ' + item.contact_no;

                            return { id: item.id, text: text };
                        })
                    };
                },
            }
        });

        $("#assist-edit-form #type_of_assistance_select2").select2({
            casesentitive: false,
            placeholder: "Select Type Of Assistance",
            allowClear: true,
            width: '100%',
            containerCssClass: ':all:',
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
            ajax: {
                url: Routing.generate('ajax_select2_assist_type'),
                data: function (params) {
                    return {
                        searchText: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.trans_type, text: item.trans_type };
                        })
                    };
                },
            }
        });

        $("#assist-edit-form #hospital_select2").select2({
            casesentitive: false,
            placeholder: "Select Hospital",
            allowClear: true,
            width: '100%',
            containerCssClass: ':all:',
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },

            ajax: {
                url: Routing.generate('ajax_select2_assist_hospital'),
                data: function (params) {
                    return {
                        searchText: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.hospital, text: item.hospital };
                        })
                    };
                },
            }
        });

        $("#assist-edit-form #diagnosis_select2").select2({
            casesentitive: false,
            placeholder: "Select Diagnosis",
            allowClear: true,
            width: '100%',
            containerCssClass: ':all:',
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },

            ajax: {
                url: Routing.generate('ajax_select2_assist_diagnosis'),
                data: function (params) {
                    return {
                        searchText: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.dependent_diagnosis, text: item.dependent_diagnosis };
                        })
                    };
                },
            }
        });

        $("#assist-edit-form #voter-select2").on("change", function () {
            console.log('setting profile id');
            console.log($(this).val());

            self.setFormPropValue('clientProfileId', $(this).val());
        });

        $("#assist-edit-form #beneficiary-select2").on("change", function () {
            console.log('setting dependent id');
            console.log($(this).val());

            self.setFormPropValue('dependentProfileId', $(this).val());
        });

        $("#assist-edit-form #type_of_assistance_select2").on("change", function () {
            self.setFormPropValue('transType', $(this).val());
        });

        $("#assist-edit-form #hospital_select2").on("change", function () {
            self.setFormPropValue('hospital', $(this).val());
        });

        $("#assist-edit-form #diagnosis_select2").on("change", function () {
            self.setFormPropValue('dependentDiagnosis', $(this).val());
        });
    },

    setFormPropValue: function (field, value) {
        var form = this.state.form;
        form.data[field] = value;
        this.setState({ form: form });
    },

    setFormProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.value;
        this.setState({ form: form });
    },

    setFormCheckProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.checked ? 1 : 0;
        this.setState({ form: form })
    },

    setErrors: function (errors) {
        var form = this.state.form;
        form.errors = errors;
        this.setState({ form: form });
    },

    getError: function (field) {
        var errors = this.state.form.errors;
        for (var errorField in errors) {
            if (errorField == field)
                return errors[field];
        }
        return null;
    },

    getValidationState: function (field) {
        return this.getError(field) != null ? 'error' : '';
    },

    isEmpty: function (value) {
        return value == null || value == '';
    },

    reset: function () {
        var form = this.state.form;
        form.errors = [];

        this.setState({ form: form });
    },

    openNewProfileModal: function () {
        var self = this;

        self.setState({ showNewProfileModal: true });
    },

    closeNewProfileModal: function () {
        var self = this;

        self.setState({ showNewProfileModal: false });
    },

    submit: function (e) {
        e.preventDefault();

        var self = this;
        var data = self.state.form.data;
        data.proId = self.props.proId;

        self.requestPost = $.ajax({
            url: Routing.generate("ajax_patch_jpm_assistance", {id : self.props.id }),
            data: data,
            type: 'PATCH'
        }).done(function (res) {
            self.reset();
            self.props.reload();
            self.props.onHide();
        }).fail(function (err) {
            self.setErrors(err.responseJSON);
        });
    }
});


window.AssistanceEditModal = AssistanceEditModal;