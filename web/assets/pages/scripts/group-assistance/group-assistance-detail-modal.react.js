var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var GroupAssistanceDetailModal = React.createClass({

    getInitialState: function () {
        return {
            showCreateModal: false,
            showDatatable: false,
            form: {
                data: {
                    batchLabel: "loading..."
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
            url: Routing.generate("ajax_get_group_assistance", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;
            form.data = res;
            self.setState({ form: form, showDatatable: true });
        });
    },

    reloadDatatable: function () {
        this.refs.detailDatatable.reload();
    },

    openCreateModal : function(){
        this.setState({
            showCreateModal : true
        });
    },

    closeCreateModal : function(){
        this.setState({ showCreateModal : false });
    },

    onSuccess : function(){
        this.reload();
    },
    
    reload: function () {
        console.log("reloading datatable");
        this.refs.detailDatatable.reload();
    },

    render: function () {
        var self = this;
        var data = self.state.form.data;
        return (
            <Modal style={{ marginTop: "10px" }} dialogClassName="modal-custom-95" keyboard={false} enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>{self.state.form.data.batchLabel}</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                    {
                        this.state.showCreateModal &&
                        <GroupAssistanceNewDetailModal
                            show={this.state.showCreateModal}
                            onHide={this.closeCreateModal}
                            onSuccess={this.onSuccess}
                            groupId={this.props.id}
                        />
                    }

                    <div className="col-md-12">
                        <button type="button" className="btn btn-success btn-sm" style={{ marginRight: "10px" }} onClick={this.openCreateModal}>Add Client</button>
                        <GroupAssistanceDetailDatatable ref="detailDatatable" groupId={this.props.id}/>
                    </div>
                </Modal.Body>
            </Modal>
        );
    }
});


window.GroupAssistanceDetailModal = GroupAssistanceDetailModal;