var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var JpmAssistanceProfileCreateModal = React.createClass({

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
            <Modal style={{ marginTop: "30px" }} keyboard={false} enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>New Profile</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">
                    <form id="assistance-profile-create-form" onSubmit={this.submit}>
                        <div className="row">
                            <div className="col-md-6">
                                <FormGroup controlId="formMunicipalityNo" validationState={this.getValidationState('municipalityNo')}>
                                    <label className="control-label">City/Municipality</label>
                                    <select id="municipality_select2" className="form-control form-filter input-sm" name="municipalityNo">
                                    </select>
                                    <HelpBlock>{this.getError('municipalityNo')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-6">
                                <FormGroup controlId="formBarangayNo" validationState={this.getValidationState('barangayNo')}>
                                    <label className="control-label">Barangay</label>
                                    <select id="barangay_select2" className="form-control form-filter input-sm" name="barangayNo">
                                    </select>
                                    <HelpBlock>{this.getError('barangayNo')}</HelpBlock>
                                </FormGroup>
                            </div>

                            {/* <div className="col-md-2" >
                                <FormGroup controlId="formPrecinctNo" validationState={this.getValidationState('precinctNo')}>
                                    <ControlLabel > Precinct No : </ControlLabel>
                                    <input type="text" value={this.state.form.data.precinctNo} className="input-sm form-control" onChange={this.setFormProp} name="precinctNo" />
                                    <HelpBlock>{this.getError('precinctNo')}</HelpBlock>
                                </FormGroup>
                            </div> */}
                        </div>

                        <div className="row">

                            <div className="col-md-6" >
                                <FormGroup controlId="formLastname" validationState={this.getValidationState('lastname')}>
                                    <ControlLabel > Lastname : </ControlLabel>
                                    <input type="text" value={this.state.form.data.lastname} className="input-sm form-control" onChange={this.setFormProp} name="lastname" />
                                    <HelpBlock>{this.getError('lastname')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-6" >
                                <FormGroup controlId="formFirstname" validationState={this.getValidationState('firstname')}>
                                    <ControlLabel > Firstname : </ControlLabel>
                                    <input type="text" value={this.state.form.data.firstname} className="input-sm form-control" onChange={this.setFormProp} name="firstname" />
                                    <HelpBlock>{this.getError('firstname')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-6" >
                                <FormGroup controlId="formMiddlename" validationState={this.getValidationState('middlename')}>
                                    <ControlLabel > Middlename : </ControlLabel>
                                    <input type="text" value={this.state.form.data.middlename} className="input-sm form-control" onChange={this.setFormProp} name="middlename" />
                                    <HelpBlock>{this.getError('middlename')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-3" >
                                <FormGroup controlId="formExtName" validationState={this.getValidationState('extName')}>
                                    <ControlLabel > Ext : </ControlLabel>
                                    <input type="text" value={this.state.form.data.extName} className="input-sm form-control" onChange={this.setFormProp} name="extName" />
                                    <HelpBlock>{this.getError('extName')}</HelpBlock>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">

                            <div className="col-md-6" >
                                <FormGroup controlId="formBirthdate" validationState={this.getValidationState('birthdate')}>
                                    <ControlLabel >Birth Date : </ControlLabel>
                                    <input type="date" className="input-sm form-control" value={this.state.form.data.birthdate} name="birthdate" onChange={this.setFormProp} />
                                    <HelpBlock>{this.getError('birthdate')}</HelpBlock>
                                </FormGroup>
                            </div>
                            <div className="col-md-6">
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

                        </div>

                        <div className="row">
                            <div className="col-md-12">
                                <FormGroup controlId="formProVoterId" validationState={this.getValidationState('proVoterId')}>
                                    <ControlLabel >Voter Name : </ControlLabel>
                                    <select id="assistance-voter-select2" className="form-control input-sm">
                                    </select>
                                    <HelpBlock>{this.getError('proVoterId')}</HelpBlock>
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

        $("#assistance-profile-create-form #assistance-voter-select2").select2({
            casesentitive: false,
            placeholder: "Enter Name...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            dropdownCssClass: 'custom-option',
            ajax: {
                url: Routing.generate('ajax_select2_project_voters'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        electId: 423,
                        proId: 3,
                        provinceCode: 53
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


        $("#assistance-profile-create-form #municipality_select2").select2({
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

        $("#assistance-profile-create-form #barangay_select2").select2({
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
                        municipalityNo: $("#assistance-profile-create-form #municipality_select2").val(),
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

        $("#assistance-profile-create-form #municipality_select2").on("change", function () {
            self.setFormPropValue("municipalityNo", $(this).val());
        });

        $("#assistance-profile-create-form #barangay_select2").on("change", function () {
            self.setFormPropValue("brgyNo", $(this).val());
        });
        
        $("#assistance-profile-create-form #assistance-voter-select2").on("change", function () {
            self.loadVoter(3,$(this).val());
        });

        var municipalityNo = this.props.municipalityNo;
        var municipalityName = this.props.municipalityName;
        var barangayNo = this.props.barangayNo;
        var barangayName = this.props.barangayName;

        if (municipalityNo != null) {

            $("#assistance-profile-create-form #municipality_select2").empty()
                .append($("<option/>")
                    .val(municipalityNo)
                    .text(municipalityName))
                .trigger("change");

            $("#assistance-profile-create-form #barangay_select2").empty()
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

            form.data.proVoterId = res.proVoterId;
            form.data.proIdCode = res.proIdCode;
            form.data.generatedIdNo = res.generatedIdNo;
            form.data.vMunicipalityName = res.municipalityName;
            form.data.vBarangayName = res.barangayName;
            form.data.voterName = res.voterName;
            form.data.isNonVoter = res.isNonVoter;
            form.data.isChecked = 1;

            console.log("voter loaded");
            console.log(res);

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


window.JpmAssistanceProfileCreateModal = JpmAssistanceProfileCreateModal;