var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var RenewId = React.createClass({

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

    componentDidMount: function () {
        this.loadUser(window.userId);
    },

    getInitialState: function () {
        return {
            municipalityName: null,
            brgyNo: null,
            showCreateModal: false
        };
    },

    loadUser: function (userId) {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_get_user", { id: userId }),
            type: "GET"
        }).done(function (res) {
            self.setState({ user: res }, self.initSelect2);
        });
    },
    
    openCreateModal : function(){
        this.setState({ showCreateModal : true})
    },

    closeCreateModal : function(){
        this.setState({ showCreateModal : false})
    },

    onSuccess : function(){
        this.reloadDatatable();
    },
    
    reloadDatatable: function () {
        console.log("reloading datatable");
            this.refs.renewedDatatable.reload();
    },

    render: function () {
        return (
            <div>
                <div className="row">
                    <div className="col-md-12">
                        <div className="portlet light portlet-fit bordered">
                            <div className="portlet-body">
                                <div className="row">
                                    <div className="col-md-12">
                                        <h4><strong>ID Renewal</strong></h4>

                                        {
                                            this.state.showCreateModal &&
                                            <RenewalCreateModal 
                                                show={this.state.showCreateModal} 
                                                notify={this.notify} 
                                                reload={this.reload} 
                                                onHide={this.closeCreateModal} 
                                                onSuccess={this.onSuccess}
                                            />
                                        }
                                            <button type="button" className="btn btn-primary" onClick={this.openCreateModal}>Renew Member</button>
                                            <RenewalDatatable ref="renewedDatatable" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
});

setTimeout(function () {
    ReactDOM.render(
        <RenewId />,
        document.getElementById('page-container')
    );
}, 500);
