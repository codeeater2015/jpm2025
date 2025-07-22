var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var AssistanceNewProfileModal = React.createClass({

    getInitialState: function () {
        return {
            form: {
                data: {
                    proVoterId: null
                },
                errors: []
            },
            provinceCode: 53
        };
    },

    render: function () {
        var self = this;

        return (
            <Modal style={{ marginTop: "60px" }} keyboard={false} dialogClassName="modal-custom-85" enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>Create New Profile</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">
                    <form id="new-voter-create-form" onSubmit={this.submit}>
                        <div className="row">
                            <div className="col-md-2">
                                <FormGroup controlId="formMunicipalityNo" validationState={this.getValidationState('municipalityNo')}>
                                    <label className="control-label">City/Municipality</label>
                                    <select id="municipality_select2" className="form-control form-filter input-sm" name="municipalityNo">
                                    </select>
                                    <HelpBlock>{this.getError('municipalityNo')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-2">
                                <FormGroup controlId="formBrgyNo" validationState={this.getValidationState('barangayNo')}>
                                    <label className="control-label">Barangay</label>
                                    <select id="barangay_select2" className="form-control form-filter input-sm" name="barangayNo">
                                    </select>
                                    <HelpBlock>{this.getError('barangayNo')}</HelpBlock>
                                </FormGroup>
                            </div>
                            <div className="col-md-2">
                                <FormGroup controlId="formDistrict" validationState={this.getValidationState('district')}>
                                    <label className="control-label">District</label>
                                    <select id="assist_district_select2" className="form-control form-filter input-sm" name="district">
                                    </select>
                                    <HelpBlock>{this.getError('district')}</HelpBlock>
                                </FormGroup>
                            </div>
                            <div className="col-md-2">
                                <FormGroup controlId="formPurok" validationState={this.getValidationState('purok')}>
                                    <label className="control-label">Purok/Sitio</label>
                                    <select id="assist_purok_select2" className="form-control form-filter input-sm" name="purok">
                                    </select>
                                    <HelpBlock>{this.getError('purok')}</HelpBlock>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">

                            <div className="col-md-2" >
                                <FormGroup controlId="formLastname" validationState={this.getValidationState('lastname')}>
                                    <ControlLabel > Lastname : </ControlLabel>
                                    <input type="text" value={this.state.form.data.lastname} className="input-sm form-control" onChange={this.setFormProp} name="lastname" />
                                    <HelpBlock>{this.getError('lastname')}</HelpBlock>
                                    <HelpBlock>{this.getError('fullname')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-2" >
                                <FormGroup controlId="formFirstname" validationState={this.getValidationState('firstname')}>
                                    <ControlLabel > Firstname : </ControlLabel>
                                    <input type="text" value={this.state.form.data.firstname} className="input-sm form-control" onChange={this.setFormProp} name="firstname" />
                                    <HelpBlock>{this.getError('firstname')}</HelpBlock>
                                    <HelpBlock>{this.getError('fullname')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-2" >
                                <FormGroup controlId="formMiddlename" validationState={this.getValidationState('middlename')}>
                                    <ControlLabel > Middlename : </ControlLabel>
                                    <input type="text" value={this.state.form.data.middlename} className="input-sm form-control" onChange={this.setFormProp} name="middlename" />
                                    <HelpBlock>{this.getError('middlename')}</HelpBlock>
                                    <HelpBlock>{this.getError('fullname')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-1" >
                                <FormGroup controlId="formExtName" validationState={this.getValidationState('extName')}>
                                    <ControlLabel > Ext : </ControlLabel>
                                    <input type="text" value={this.state.form.data.extName} className="input-sm form-control" onChange={this.setFormProp} name="extName" />
                                    <HelpBlock>{this.getError('extName')}</HelpBlock>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-md-6">
                                <FormGroup controlId="formVoterName" validationState={this.getValidationState('proVoterId')}>
                                    <ControlLabel > Comelec Name : </ControlLabel>
                                    <select id="comelec-voter-select2" className="form-control input-sm">
                                    </select>
                                    <HelpBlock>{this.getError('proVoterId')}</HelpBlock>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-md-2" >
                                <FormGroup controlId="formBirthdate" validationState={this.getValidationState('birthdate')}>
                                    <ControlLabel > Birthdate : </ControlLabel>
                                    <input type="date" value={this.state.form.data.birthdate} className="input-sm form-control" onChange={this.setFormProp} name="birthdate" />
                                    <HelpBlock>{this.getError('birthdate')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-2">
                                <FormGroup controlId="formGender" validationState={this.getValidationState('gender')}>
                                    <ControlLabel > Gender : </ControlLabel>
                                    <select className="input-sm form-control" onChange={this.setFormProp} name="gender">
                                        <option value="">- Select -</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                    <HelpBlock>{this.getError('gender')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-2">
                                <FormGroup controlId="formCivil" validationState={this.getValidationState('civilStatus')}>
                                    <label className="control-label">Civil Status</label>
                                    <select id="assist_civil_status_select2" className="form-control form-filter input-sm" name="civilStatus">
                                    </select>
                                    <HelpBlock>{this.getError('civilStatus')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-2" >
                                <FormGroup controlId="formContactNo" validationState={this.getValidationState('contactNo')}>
                                    <ControlLabel > Contact No : </ControlLabel>
                                    <input type="text" placeholder="Example : 09283182013" value={this.state.form.data.contactNo} className="input-sm form-control" onChange={this.setFormProp} name="contactNo" />
                                    <HelpBlock>{this.getError('contactNo')}</HelpBlock>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-md-2">
                                <div className="form-group">
                                    <label className="control-label">Trabaho</label>
                                    <select id="assist_occupation_select2" className="form-control form-filter input-sm" name="occupation">
                                    </select>
                                </div>
                            </div>
                            <div className="col-md-2" >
                                <FormGroup controlId="formMonthlyIncome" validationState={this.getValidationState('monthlyIncome')}>
                                    <ControlLabel > Buwanang Kita : </ControlLabel>
                                    <input type="text" placeholder="Example : 09283182013" value={this.state.form.data.monthlyIncome} className="input-sm form-control" onChange={this.setFormProp} name="monthlyIncome" />
                                    <HelpBlock>{this.getError('monthlyIncome')}</HelpBlock>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-md-12 text-right">
                                <button className="btn btn-primary btn-sm" style={{ marginRight: "10px" }} type="submit"> Submit </button>
                                <button className="btn btn-default btn-sm" type="button" onClick={this.props.onHide} > Close </button>
                            </div>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        );
    },

    componentDidMount: function () {
        this.initSelect2();
    },

    initSelect2: function () {
        var self = this;

        $("#new-voter-create-form #comelec-voter-select2").select2({
            casesentitive: false,
            placeholder: "Select Applicant Name",
            allowClear: true,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_project_voters'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        proId: 3,
                        electId: 423,
                        provinceCode : 53,
                        municipalityNo :  $("#new-voter-create-form #municipality_select2").val(),
                        brgyNo : $("#new-voter-create-form #barangay_select2").val()
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            var text = item.voter_name + ' - ' + item.precinct_no + ' ( ' + item.municipality_name + ', ' + item.barangay_name + ' )';
                            return { id: item.pro_voter_id, text: text };
                        })
                    };
                },
            }
        });

        $("#new-voter-create-form #municipality_select2").select2({
            casesentitive: false,
            placeholder: "Select City/Municipality",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_municipality'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        provinceCode: self.state.provinceCode
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.municipality_no, text: item.name };
                        })
                    };
                },
            }
        });

        $("#new-voter-create-form #barangay_select2").select2({
            casesentitive: false,
            placeholder: "Select Barangay",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_barangay'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        municipalityNo: $("#new-voter-create-form #municipality_select2").val(),
                        provinceCode: self.state.provinceCode
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.brgy_no, text: item.name };
                        })
                    };
                },
            }
        });

        $("#new-voter-create-form #assist_district_select2").select2({
            casesentitive: false,
            placeholder: "Select...",
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
                url: Routing.generate('ajax_select2_assist_district'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.district, text: item.district };
                        })
                    };
                },
            }
        });

        $("#new-voter-create-form #assist_purok_select2").select2({
            casesentitive: false,
            placeholder: "Select...",
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
                url: Routing.generate('ajax_select2_assist_purok'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.purok, text: item.purok };
                        })
                    };
                },
            }
        });


        $("#new-voter-create-form #assist_civil_status_select2").select2({
            casesentitive: false,
            placeholder: "Select...",
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
                url: Routing.generate('ajax_select2_assist_civil'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.civil_status, text: item.civil_status };
                        })
                    };
                },
            }
        });

        $("#new-voter-create-form #assist_occupation_select2").select2({
            casesentitive: false,
            placeholder: "Select...",
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
                url: Routing.generate('ajax_select2_assist_occupation'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.occupation, text: item.occupation };
                        })
                    };
                },
            }
        });


        $("#new-voter-create-form #assist_district_select2").on("change", function () {
            self.setFormPropValue("district", $(this).val());
        });

        $("#new-voter-create-form #assist_purok_select2").on("change", function () {
            self.setFormPropValue("purok", $(this).val());
        });

        $("#new-voter-create-form #assist_civil_status_select2").on("change", function () {
            console.log("civil status changed");
            self.setFormPropValue("civilStatus", $(this).val());
        });

        $("#new-voter-create-form #assist_occupation_select2").on("change", function () {
            self.setFormPropValue("occupation", $(this).val());
        });

        $("#new-voter-create-form #municipality_select2").on("change", function () {
            self.setFormPropValue("municipalityNo", $(this).val());
        });

        $("#new-voter-create-form #barangay_select2").on("change", function () {
            self.setFormPropValue("brgyNo", $(this).val());
        });

        $("#new-voter-create-form #comelec-voter-select2").on("change", function () {
            self.setFormPropValue("proVoterId", $(this).val());
            self.loadVoter(3, $(this).val());
        });

        var municipalityNo = this.props.municipalityNo;
        var municipalityName = this.props.municipalityName;
        var barangayNo = this.props.barangayNo;
        var barangayName = this.props.barangayName;

        if (municipalityNo != null) {

            $("#new-voter-create-form #municipality_select2").empty()
                .append($("<option/>")
                    .val(municipalityNo)
                    .text(municipalityName))
                .trigger("change");

            $("#new-voter-create-form #barangay_select2").empty()
                .append($("<option/>")
                    .val(barangayNo)
                    .text(barangayName))
                .trigger("change");
        }
    },

    loadVoter: function (proId, proVoterId) {
        var self = this;

        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_project_voter", { proId: proId, proVoterId: proVoterId }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;

            form.data.vMunicipalityNo = res.municipalityNo;
            form.data.vMunicipalityName = res.municipalityName;
            form.data.vBarangayName = res.barangayName;
            form.data.vBrgyNo = res.brgyNo;
            form.data.proVoterId = res.proVoterId;
            form.data.voterName = res.voterName;
            form.data.isNonVoter = res.isNonVoter;
            form.data.generatedIdNo = res.generatedIdNo;
            form.data.proIdCode = res.proIdCode;

            self.setState({ form: form });
        });
    },

    setFormPropValue: function (field, value) {
        var form = this.state.form;
        form.data[field] = value;
        this.setState({ form: form });
    },

    setFormProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.value.toUpperCase();
        this.setState({ form: form });
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

    notify: function (message, color) {
        $.notific8('zindex', 11500);
        $.notific8(message, {
            heading: 'System Message',
            color: color,
            life: 5000,
            verticalEdge: 'right',
            horizontalEdge: 'top',
        });
    },

    submit: function (e) {
        e.preventDefault();

        var self = this;
        var data = self.state.form.data;

        data.electId = self.props.electId;
        data.proId = self.props.proId;

        self.requestPost = $.ajax({
            url: Routing.generate("ajax_post_assistance_profile"),
            data: data,
            type: 'POST'
        }).done(function (res) {
            self.props.onHide();
            self.props.onSuccess(res);
            self.notify("New record has been saved.", 'teal');
        }).fail(function (err) {
            self.setErrors(err.responseJSON);
            self.notify("Form validation failed!.", 'ruby');
        });
    }
});


window.AssistanceNewProfileModal = AssistanceNewProfileModal;