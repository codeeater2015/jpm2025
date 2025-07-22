
var Tab = ReactBootstrap.Tab;
var Tabs = ReactBootstrap.Tabs;
var Nav = ReactBootstrap.Nav;
var NavItem = ReactBootstrap.NavItem;

var GroupAssistance = React.createClass({

    getInitialState: function () {
        return {
            active: "ASSIST",
            showCreateModal: false,
            showNewProfileModal : false,
            showSmsModal: false,
            form: {
                data: {}
            }
        }
    },

    setSelectedTab: function (key) {
        this.setState({ active: key });
    },

    openCreateModal: function () {
        console.log("open sms modal");
        this.setState({ showCreateModal: true });
        console.log(this.state.showCreateModal);
    },

    openProfileCreateModal: function () {
        this.setState({ showNewProfileModal: true });
    },

    closeProfileCreateModal: function () {
        this.setState({ showNewProfileModal: false });
        this.refs.ProfilesDatatable.reload();
    },

    closeCreateModal: function () {
        this.setState({ showCreateModal: false });
    },

    onCreateSuccess: function () {
        this.refs.attendanceDatatable.reload();
        this.setState({ showCreateModal: false });
    },

    onSuccess: function () {
        this.reload();
        this.closeCreateModal();
    },

    reload: function () {
        console.log("reloading datatable");
        this.refs.groupDatatable.reload();
    },

    render: function () {
        var self = this;

        return (
            <div className="portlet light portlet-fit bordered">
                <div className="portlet-body" id="bcbp_component">

                    {
                        this.state.showCreateModal &&
                        <GroupAssistanceCreateModal
                            show={this.state.showCreateModal}
                            onHide={this.closeCreateModal}
                            onSuccess={this.onSuccess}
                        />
                    }
                  
                    <Tab.Container activeKey={this.state.active} onSelect={this.setSelectedTab}>
                        <div className="row" >
                            <div className="col-md-12 m-t-sm">
                                <Nav bsStyle="tabs">
                                    <NavItem eventKey="ASSIST">
                                        Assistance
                                    </NavItem>
                                    <NavItem eventKey="PROFILES">
                                        Profiles
                                    </NavItem>

                                </Nav>
                            </div>
                            <div className="col-md-12 m-t-sm">
                                <Tab.Content animation>
                                    <Tab.Pane eventKey="ASSIST">
                                        {this.state.active == "ASSIST" &&
                                            <div>
                                                <div className="row">
                                                    <div className="col-md-12">
                                                        <button type="button" className="btn btn-success btn-sm" style={{ marginRight: "10px" }} onClick={this.openCreateModal}>New Assistance</button>
                                                    </div>
                                                </div>
                                                <div className="row">
                                                   <GroupAssistanceDatatable ref="groupDatatable"/>
                                                </div>
                                            </div>
                                        }
                                    </Tab.Pane>
                                    <Tab.Pane eventKey="PROFILES">
                                        {this.state.active == "PROFILES" &&
                                            <div>
                                                <div className="row">
                                                    <div className="col-md-12">
                                                        <button type="button" className="btn btn-success btn-sm" style={{ marginRight: "10px" }} onClick={this.openProfileCreateModal}>New Profile</button>
                                                    </div>
                                                </div>
                                                <div className="row">
                                                    <ProfilesDatatable ref="ProfilesDatatable" />
                                                </div>
                                            </div>
                                        }

                                    </Tab.Pane>
                                </Tab.Content>
                            </div>
                        </div>
                    </Tab.Container>
                </div>
            </div>
        )
    }
});

setTimeout(function () {
    ReactDOM.render(
        <GroupAssistance />,
        document.getElementById('page-container')
    );
}, 500);
