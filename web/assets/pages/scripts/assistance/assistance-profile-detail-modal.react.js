var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var AssistanceProfileDetailModal = React.createClass({

    getInitialState: function () {
        return {
            showCreateModal: false,
            showDatatable : false,
            form: {
                data: {
                    fullname: "loading..."
                }
            }
        };
    },

    componentDidMount: function () {
        this.loadData(this.props.id);
    },

    loadData: function (id) {
        var self = this;

        self.requestProfile = $.ajax({
            url: Routing.generate("ajax_get_assistance_profile", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;
            form.data = res;
            self.setState({ form: form, showDatatable : true });
        });
    },

    reloadDatatable: function () {
        this.refs.detailDatatable.reload();
    },

    render: function () {
        var self = this;
        var data = self.state.form.data;
        return (
            <Modal style={{ marginTop: "10px" }} dialogClassName="modal-custom-85" keyboard={false} enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>{self.state.form.data.fullname}</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">
                    <div className="row">
                        <div className="col-md-8">
                            <div><strong>Profile Name : </strong> {data.fullname} </div>
                            <div><strong>Voter Name : </strong> {data.voterName} </div>
                            <br/>
                            <div><strong>Birthdate : </strong> {data.birthdate} </div>
                            <div><strong>Gender : </strong> {data.gender} </div>
                            <div><strong>Civil Status: </strong> {data.civilStatus} </div>
                            <br/>
                            <div><strong>Trabaho: </strong> {data.occupation} </div>
                            <div><strong>Monthly Income : </strong> {data.monthlyIncome} </div>
                        </div>
                        <div className="col-md-4">
                            <div><strong>District : </strong> {data.district} </div>
                            <div><strong>Municipality : </strong> {data.municipalityName} </div>
                            <div><strong>Barangay : </strong> {data.barangayName} </div>
                            <div><strong>Purok : </strong> {data.purok} </div>
                        </div>
                    </div>
                    <div className="clearfix"></div>
                    <br/>
                    <div className="row">
                        <div className="col-md-12">
                            {
                                self.state.showDatatable &&
                                <ProfileAssistanceDatatable fullname={self.state.form.data.fullname} ref="ProfileDetailDatatable"/>
                            }
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
        );
    }
});


window.AssistanceProfileDetailModal = AssistanceProfileDetailModal;