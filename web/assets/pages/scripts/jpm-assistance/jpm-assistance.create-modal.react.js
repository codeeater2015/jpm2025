var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;


var JpmAssistanceCreateModal = React.createClass({

    getInitialState: function () {
        return {
            showNewProfileModal: false,
            form: {
                data: {
                    municipalityNo: "",
                    barangayNo: "",
                    meetingDate: "",
                    description: "",
                    meetingGroup : "",
                    meetingPosition : ""
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
                    <Modal.Title>New Assistance List</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                    <form id="jpm-assistance-form" >
                        <div className="row">
                            <div className="col-md-12">

                                <div className="row">
                                    <div className="col-md-3">
                                        <FormGroup controlId="formMunicipality" validationState={this.getValidationState('municipalityNo')}>
                                            <label className="control-label">City/Municipality</label>
                                            <select id="municipality_select2" className="form-control form-filter input-sm" name="municipalityNo">
                                            </select>
                                            <HelpBlock>{this.getError('municipalityNo')}</HelpBlock>
                                        </FormGroup>
                                    </div>

                                    <div className="col-md-3">
                                        <FormGroup controlId="formBarangay" validationState={this.getValidationState('barangayNo')}>
                                            <label className="control-label">Barangay</label>
                                            <select id="barangay_select2" className="form-control form-filter input-sm" name="brgyNo">
                                            </select>
                                            <HelpBlock>{this.getError('barangayNo')}</HelpBlock>
                                        </FormGroup>
                                    </div>

                                    <div className="col-md-3">
                                        <FormGroup controlId="formAssistType" validationState={this.getValidationState('assistType')}>
                                            <label className="control-label">Type of Assistance</label>
                                            <select id="assist_type_select2" className="form-control form-filter input-sm" name="assistType">
                                            </select>
                                            <HelpBlock>{this.getError('assistType')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-3" >
                                        <FormGroup controlId="formReleaseDate" validationState={this.getValidationState('releaseDate')}>
                                            <ControlLabel >Release Date : </ControlLabel>
                                            <input type="date" className="input-md form-control" value={this.state.form.data.releaseDate} name="releaseDate" onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('releaseDate')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-12" >
                                        <FormGroup controlId="formAssistDesc" validationState={this.getValidationState('assistDesc')}>
                                            <ControlLabel > Assistance Description : </ControlLabel>
                                            <textarea type="text" rows="3" value={this.state.form.data.assistDesc} name="assistDesc" className="input-md form-control" onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('assistDesc')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="clearfix" />

                        <div className="text-right" style={{ marginTop: "30px" }}>
                            <button type="button" className="btn-lg btn-default" style={{ marginRight: "5px" }} onClick={this.props.onHide}>Close</button>
                            <button type="button" className="btn-lg btn-primary" onClick={this.submit}>Submit</button>
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

        $("#jpm-assistance-form #municipality_select2").select2({
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
                        provinceCode: 53
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

        $("#jpm-assistance-form #barangay_select2").select2({
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
                        municipalityNo: $("#jpm-assistance-form #municipality_select2").val(),
                        provinceCode: 53
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

        $("#jpm-assistance-form #assist_type_select2").select2({
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
                url: Routing.generate('ajax_select2_assist_type'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.assist_type, text: item.assist_type };
                        })
                    };
                },
            }
        });


        $("#jpm-assistance-form #municipality_select2").on("change", function () {
            self.setFormPropValue('municipalityNo', $(this).val());
        });

        $("#jpm-assistance-form #barangay_select2").on("change", function () {
            self.setFormPropValue('barangayNo', $(this).val());
        });
       
        $("#jpm-assistance-form #assist_type_select2").on("change", function () {
            self.setFormPropValue('assistType', $(this).val());
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

        form.data.firstname = "";
        form.data.lastname = "";
        form.data.nickname = "";
        form.data.birthdate = "";
        form.data.contactNumber = "";

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

        console.log("form data");
        console.log(self.state.form.data);

        self.requestPost = $.ajax({
            url: Routing.generate("ajax_post_jpm_assistance"),
            data: data,
            type: 'POST'
        }).done(function (res) {
            self.reset();
            self.props.onSuccess();
        }).fail(function (err) {
            self.setErrors(err.responseJSON);
        });
    }
});


window.JpmAssistanceCreateModal = JpmAssistanceCreateModal;