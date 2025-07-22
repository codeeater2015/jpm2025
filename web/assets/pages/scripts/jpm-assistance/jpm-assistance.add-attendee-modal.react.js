var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var JpmAssistanceAddAttendeeModal = React.createClass({

    getInitialState: function () {
        return {
            showNewProfileModal: false,
            showNewVoterCreateModal : false,
            form: {
                data: {
                    contactNo: "",
                    proVoterId: null
                },
                errors: []
            }
        };
    },


    openNewVoterCreateModal: function () {
        console.log('opening modal');
        this.setState({ showNewVoterCreateModal: true })
    },

    closeNewVoterCreateModal: function () {
        this.setState({ showNewVoterCreateModal: false });
    },

    render: function () {
        var self = this;
        var data = this.state.form.data;

        return (
            <Modal style={{ marginTop: "100px" }} keyboard={false} enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>New Attendee</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                {
                        this.state.showNewVoterCreateModal &&
                        <JpmAssistanceProfileCreateModal
                            show={this.state.showNewVoterCreateModal}
                            onHide={this.closeNewVoterCreateModal}
                        />
                    }

                    <form id="assistance-attendance-form" onSubmit={this.submit}>
                        <div className="row">
                            <div className="col-md-9">
                                <FormGroup controlId="formProVoterId" validationState={this.getValidationState('proVoterId')}>
                                    <ControlLabel >Name : </ControlLabel>
                                    <select id="profile-select2" className="form-control input-sm">
                                    </select>
                                    <HelpBlock>{this.getError('proVoterId')}</HelpBlock>
                                </FormGroup>
                            </div>
                            <div className="col-md-3">
                                <button style={{ marginTop: "25px" }} onClick={this.openNewVoterCreateModal} className="btn btn-primary btn-sm" type="button"> New Profile </button>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-md-4" >
                                <FormGroup controlId="formCellphoneNo" validationState={this.getValidationState('cellphoneNo')}>
                                    <ControlLabel > Cellphone No: </ControlLabel>
                                    <input type="text" value={this.state.form.data.cellphoneNo} name="cellphoneNo" className="input-md form-control" onChange={this.setFormProp} />
                                    <HelpBlock>{this.getError('cellphoneNo')}</HelpBlock>
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

        $("#assistance-attendance-form #profile-select2").select2({
            casesentitive: false,
            placeholder: "Enter Name...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            dropdownCssClass: 'custom-option',
            ajax: {
                url: Routing.generate('ajax_select2_assistance_profiles'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                          
                            var text = item.fullname + " (" + item.barangay_name + ", " + item.municipality_name + ")";

                            return { id: item.profile_id, text: text };
                        })
                    };
                },
            }
        });

        $("#assistance-attendance-form #profile-select2").on("change", function () {
            self.loadProfile($(this).val());
        });
    },

    loadProfile: function (profileId) {
        var self = this;
        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_assistance_profile", { profileId: profileId }),
            type: "GET"
        }).done(function (res) {

            var form = self.state.form;

            form.data.profileId = res.id;
            form.data.cellphoneNo = res.cellphoneNo;
            console.log("profile has been received", res);

            self.setState({ form: form });
        });

        var form = self.state.form;

        form.data.profileId = null;
        form.data.cellphoneNo = '';

        self.setState({ form: form })
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

        form.data.contactNo = "";
        form.data.proVoterId = null;

        $("#assistance-attendance-form#profile-select2").empty().trigger("change");

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
            url: Routing.generate("ajax_post_assistance_detail", { id: this.props.id }),
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


window.JpmAssistanceAddAttendeeModal = JpmAssistanceAddAttendeeModal;