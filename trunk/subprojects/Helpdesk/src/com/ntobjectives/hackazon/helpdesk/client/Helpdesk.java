package com.ntobjectives.hackazon.helpdesk.client;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.core.client.GWT;
import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.event.logical.shared.ValueChangeEvent;
import com.google.gwt.event.logical.shared.ValueChangeHandler;
import com.google.gwt.i18n.client.DateTimeFormat;
import com.google.gwt.safehtml.shared.SafeHtmlUtils;
import com.google.gwt.user.client.History;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.rpc.AsyncCallback;
import com.google.gwt.user.client.ui.*;
import com.ntobjectives.hackazon.helpdesk.client.entity.ApplicationData;
import com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry;
import com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage;
import com.ntobjectives.hackazon.helpdesk.client.entity.User;

import java.util.ArrayList;

/**
 * Entry point classes define <code>onModuleLoad()</code>
 */
public class Helpdesk implements EntryPoint, ValueChangeHandler<String> {
    String startingToken = "";
    private Boolean isAuthenticated = null;

    private VerticalPanel mainPanel = new VerticalPanel();
    private FlexTable enquiriesTable = new FlexTable();
    private BSLinkButton addEnquiryButton = new BSLinkButton("#add-enquiry", "Add Enquiry");
    private RootPanel rootPanel;
    private final HorizontalPanel buttonPanel = new HorizontalPanel();
    private final HTML errors = new HTML();
    final FlowPanel messagePanel = new FlowPanel();
    RootPanel breadcrumbs;
    BreadcrumbsCollection bcCollection = new BreadcrumbsCollection();
    User user;
    DateTimeFormat dateFormat = DateTimeFormat.getFormat("mm/dd/yyyy h:mm");



    /**
     * This is the entry point method.
     */
    public void onModuleLoad() {
        HelpdeskService.App.getInstance().getAppData(new GetAppDataAsyncCallback(this));
    }

    public void init () {
        rootPanel = RootPanel.get("helpdesk");
        breadcrumbs = RootPanel.get("breadcrumbs");
        breadcrumbs.getElement().setInnerHTML("");
        rootPanel.getElement().addClassName("helpdesk");
        rootPanel.getElement().setInnerHTML("");
        buttonPanel.getElement().addClassName("buttons-panel");
        buttonPanel.setHorizontalAlignment(HasHorizontalAlignment.ALIGN_RIGHT);

        errors.getElement().addClassName("errors alert alert-danger");
        errors.setVisible(false);

        messagePanel.getElement().addClassName("messages-panel");

        // Process hashchange
        startingToken = History.getToken();

        History.addValueChangeHandler(this);
        History.newItem(startingToken, true);
        History.fireCurrentHistoryState();
    }

    @Override
    public void onValueChange(ValueChangeEvent<String> event) {
        executeInPanel(rootPanel, event.getValue());
    }

    public void executeInPanel(Panel myPanel, String token) {
        String args = "";
        token = token == null ? "" : token;
        int question = token.indexOf("?");
        if (question != -1) {
            args = token.substring(question + 1);
            token = token.substring(0, question);
        }

        String[] parts = token.split("/");

        rootPanel.clear();
        mainPanel.clear();
        mainPanel.getElement().setClassName("main-panel");
        buttonPanel.clear();
        errors.setHTML("");
        errors.setVisible(false);
        rootPanel.add(errors);
        bcCollection.clear();

        GWT.log("Token is: " + token);

        if (token.isEmpty()) {
            showMainAction();
        } else if (token.equals("add-enquiry")) {
            showAddEnquiryFormAction();
        } else if (token.matches("enquiry/\\d+")) {
            showEnquiryAction(new Integer(parts[1]));

        } else {
            showNotFoundAction();
        }
    }

    public void showMainAction() {
        bcCollection.add("Helpdesk", null, true);
        buildBreadcrumbs();

        final HTML label = new HTML("<p>Loading enquiries...</p>");
        mainPanel.add(label);
        rootPanel.add(mainPanel);
        HelpdeskService.App.getInstance().getEnquiries(new EnquiriesAsyncCallback(mainPanel, this));

        buttonPanel.add(addEnquiryButton);
        rootPanel.add(buttonPanel);
    }

    public void showAddEnquiryFormAction() {
        bcCollection.add("Helpdesk", "#");
        bcCollection.add("Add Enquiry", null, true);
        buildBreadcrumbs();

        FlowPanel fp = new FlowPanel();
        fp.getElement().addClassName("form-panel add-enquiry-form");

        Label titleLabel = new Label("Title:");
        final TextBox title = new TextBox();
        title.getElement().setClassName("form-control");
        title.getElement().setAttribute("required", "required");

        Label descLabel = new Label("Description:");
        final TextArea description = new TextArea();
        description.getElement().setClassName("form-control");
        description.getElement().setAttribute("required", "required");

        BSButton submit = new BSButton("Submit");

        submit.addClickHandler(new ClickHandler() {
            @Override
            public void onClick(ClickEvent event) {
                Enquiry enq = new Enquiry();
                enq.setTitle(title.getValue());
                enq.setDescription(description.getValue());
                HelpdeskService.App.getInstance().createEnquiry(enq, new NewEnquiryAsyncCallback(errors));
            }
        });

        rootPanel.add(errors);
        fp.add(titleLabel);
        fp.add(title);
        fp.add(descLabel);
        fp.add(description);
        fp.add(new HTML("<br>"));

        buttonPanel.add(submit);

        mainPanel.add(fp);
        rootPanel.add(mainPanel);
        rootPanel.add(buttonPanel);
    }

    public void showEnquiryAction(int id) {
        bcCollection.add("Helpdesk", "#");
        bcCollection.add("Enquiry №" + id, null, true);
        buildBreadcrumbs();
        HelpdeskService.App.getInstance().getEnquiryById(id, new ShowEnquiryAsyncCallback(this));
    }

    public void showEnquiryAction(Enquiry enquiry) {
        VerticalPanel enquiryPanel = new VerticalPanel();
        HTML enqDescription = new HTML(
              "<h3>" + SafeHtmlUtils.htmlEscape(enquiry.getTitle()) + " <span class=\"label label-"
                    + SafeHtmlUtils.htmlEscape(enquiry.getStatus()) + "\">"
                    + SafeHtmlUtils.htmlEscape(enquiry.getStatus()) + "</span></h3>"
            + "<p>" + SafeHtmlUtils.htmlEscape(enquiry.getDescription()).replace("\n", "<br>") + "</p><hr>"
            + "<h4>Messages:</h4>"
        );

        enquiryPanel.getElement().addClassName("enquiry-page");
        enquiryPanel.add(enqDescription);

        Panel formPanel = createAddMessageForm(enquiry);
        messagePanel.clear();
        messagePanel.add(new HTML("<p>Loading messages...</p>"));
        rootPanel.add(enquiryPanel);
        rootPanel.add(messagePanel);
        rootPanel.remove(errors);
        rootPanel.add(errors);
        rootPanel.add(formPanel);

        requestShowEnquiryMessages(enquiry, messagePanel);
    }

    public void requestShowEnquiryMessages(Enquiry enquiry, Panel panel) {
        HelpdeskService.App.getInstance().getEnquiryMessages(enquiry.getId(), new ShowEnquiryMessagesAsyncCallback(this, panel));
    }

    public void showEnquiryMessages(ArrayList<EnquiryMessage> messages, Panel panel) {
        panel.clear();
        StringBuilder sb = new StringBuilder();

        if (messages.size() == 0) {
            panel.add(new HTML("<p>There are no messages in this enquiry. Please write a new one.</p>"));

        } else {
            sb.append("<ul class=\"chat\">");
            for (EnquiryMessage msg : messages) {
                String side = user.getId() == msg.getAuthorId() ? "left" : "right";
                String color = side.equals("left") ? "55C1E7" : "FA6F57";
                sb.append("<li class=\"").append(side).append(" clearfix\">")
                        .append("<span class=\"chat-img pull-").append(side).append("\">\n")
                            .append("<img class=\"img-circle\" alt=\"User Avatar\" src=\"http://placehold.it/50/").append(color).append("/fff\">\n")
                        .append("</span>")
                        .append("<div class=\"chat-body clearfix\">\n")
                            .append("<div class=\"header\">\n")
                                .append("<strong class=\"").append(side.equals("left") ? "" : "pull-right").append(" primary-font\">").append(msg.getAuthorName()).append("</strong>\n" +
                                        "<small class=\"").append(side.equals("left") ? "pull-right" : "").append(" text-muted\">\n<i class=\"fa fa-clock-o fa-fw\"></i> ")
                                            .append(msg.getCreatedOn() != null ? dateFormat.format(msg.getCreatedOn()) : "")
                                        .append("\n</small>\n")
                            .append("</div>\n")
                            .append("<p>\n")
                                .append(SafeHtmlUtils.htmlEscape(msg.getMessage()).replace("\n", "<br>"))
                            .append("</p>\n")
                        .append("</div>")
                    .append("</li>");
            }
            sb.append("</ul>");
            panel.add(new HTML(sb.toString()));
        }
    }

    public void showNotFoundAction() {
        bcCollection.add("Helpdesk", "#");
        bcCollection.add("Enquiry doesn't exist.", null, true);
        buildBreadcrumbs();

        HTML html = new HTML("<h3>Page Not Found</h3>");
        mainPanel.add(html);
        rootPanel.add(mainPanel);
    }

    protected Panel createAddMessageForm(final Enquiry enquiry) {
        VerticalPanel panel = new VerticalPanel();
        panel.getElement().addClassName("add-message-form");

        Label descLabel = new Label("Description:");
        final TextArea description = new TextArea();
        description.getElement().setClassName("form-control");
        description.getElement().setAttribute("required", "required");

        BSButton submit = new BSButton("Submit");
        final Helpdesk hd = this;

        submit.addClickHandler(new ClickHandler() {
            @Override
            public void onClick(ClickEvent event) {
                EnquiryMessage mess = new EnquiryMessage();
                mess.setMessage(description.getValue());
                mess.setEnquiry(enquiry);
                HelpdeskService.App.getInstance().createEnquiryMessage(mess, new NewEnquiryMessageAsyncCallback(hd, errors, messagePanel, enquiry, description));
            }
        });

        panel.add(descLabel);
        panel.add(description);
        buttonPanel.add(submit);
        panel.add(buttonPanel);

        return panel;
    }

    protected void createEnquiryTable() {
        enquiriesTable.setText(0, 0, "ID");
        enquiriesTable.setText(0, 1, "Title");
        enquiriesTable.setText(0, 2, "Status");
        enquiriesTable.getCellFormatter().getElement(0, 0).addClassName("id-column");
        enquiriesTable.getCellFormatter().getElement(0, 2).addClassName("status-column");
        enquiriesTable.getRowFormatter().getElement(0).addClassName("table-header");
        enquiriesTable.addStyleName("table enquiry-table");
        mainPanel.addStyleName("table helpdesk-layout");
    }

    public void showEnquiryTable(ArrayList<Enquiry> list) {
        enquiriesTable.clear();
        createEnquiryTable();

        for (int i = 0; i < list.size(); i++) {
            Enquiry enquiry = list.get(i);
            enquiriesTable.setText(i + 1, 0, "" + enquiry.getId());
            enquiriesTable.setHTML(i + 1, 1, "<a href=\"#enquiry/" + enquiry.getId() + "\">" + enquiry.getTitle() + "</a>");
            enquiriesTable.setHTML(i + 1, 2, "<span class=\"label label-" + enquiry.getStatus() + "\">" + enquiry.getStatus() + "</span>");
        }

        mainPanel.clear();
        mainPanel.add(enquiriesTable);
    }

    public void setUser(User user) {
        this.user = user;
    }

    public User getUser() {
        return user;
    }

    private static class EnquiriesAsyncCallback implements AsyncCallback<ArrayList<Enquiry>> {
        private Panel panel;
        private Helpdesk helpdesk;

        public EnquiriesAsyncCallback(Panel mainPanel, Helpdesk helpdesk) {
            panel = mainPanel;
            this.helpdesk = helpdesk;
        }

        public void onSuccess(ArrayList<Enquiry> result) {
            int size = result.size();
            if (size == 0) {
                helpdesk.mainPanel.clear();
                onFailure(new IllegalArgumentException(""));
                return;
            }

            helpdesk.showEnquiryTable(result);
        }

        public void onFailure(Throwable throwable) {
            Label label = new Label("You do not have any enquiries.");
            panel.add(label);
        }
    }

    private static class NewEnquiryAsyncCallback implements AsyncCallback<Enquiry> {
        private HTML errors;

        public NewEnquiryAsyncCallback(HTML errors) {
            this.errors = errors;
        }

        public void onSuccess(Enquiry enquiry) {
            History.newItem("enquiry/" + enquiry.getId(), true);
        }

        public void onFailure(Throwable throwable) {
            errors.setHTML("Please enter title and description of your enquiry.");
            errors.setVisible(true);
        }
    }

    private static class NewEnquiryMessageAsyncCallback implements AsyncCallback<EnquiryMessage> {
        private final Helpdesk helpdesk;
        private final Panel panel;
        private final Enquiry enquiry;
        private final TextArea description;
        private HTML errors;

        public NewEnquiryMessageAsyncCallback(Helpdesk helpdesk, HTML errors, Panel panel, Enquiry enquiry, TextArea description) {
            this.errors = errors;
            this.helpdesk = helpdesk;
            this.panel = panel;
            this.enquiry = enquiry;
            this.description = description;
        }

        public void onSuccess(EnquiryMessage msg) {
            errors.setVisible(false);
            description.setValue("");
            helpdesk.requestShowEnquiryMessages(enquiry, panel);
        }

        public void onFailure(Throwable throwable) {
            helpdesk.showError("Please enter a message");
        }
    }


    private static class ShowEnquiryAsyncCallback implements AsyncCallback<Enquiry> {
        private Helpdesk helpdesk;

        public ShowEnquiryAsyncCallback(Helpdesk helpdesk) {
            this.helpdesk = helpdesk;
        }

        public void onSuccess(Enquiry enquiry) {
            if (enquiry == null) {
                helpdesk.showNotFoundAction();
            }
            helpdesk.showEnquiryAction(enquiry);
        }

        public void onFailure(Throwable throwable) {
            helpdesk.showNotFoundAction();
        }
    }

    private static class ShowEnquiryMessagesAsyncCallback implements AsyncCallback<ArrayList<EnquiryMessage>> {
        private final Panel panel;
        private Helpdesk helpdesk;

        public ShowEnquiryMessagesAsyncCallback(Helpdesk helpdesk, Panel panel) {
            this.helpdesk = helpdesk;
            this.panel = panel;
        }

        public void onSuccess(ArrayList<EnquiryMessage> messages) {
            if (messages == null) {
                showNoMessages();
            }
            helpdesk.showEnquiryMessages(messages, panel);
        }

        public void onFailure(Throwable throwable) {
            showNoMessages();
        }

        protected void showNoMessages() {
            panel.add(new HTML("There are no messages in this discussion."));
        }
    }

    private static class GetAppDataAsyncCallback implements AsyncCallback<ApplicationData> {
        Helpdesk hd;

        public GetAppDataAsyncCallback(Helpdesk helpdesk) {
            hd = helpdesk;
        }

        public void onSuccess(ApplicationData result) {
            if (result.isAutorized()) {
                hd.setAuthenticated(true);
                hd.setUser(result.getUser());
                hd.init();
            } else {
                Window.Location.assign("/user/login?redirect_url=/helpdesk");
            }
        }

        public void onFailure(Throwable throwable) {
            Window.alert("Error while checking authentication.");
        }
    }

    public boolean isAuthenticated() {
        return isAuthenticated;
    }

    public void setAuthenticated(boolean value) {
        isAuthenticated = value;
    }

    public void showError(String error) {
        errors.setHTML(error);
        errors.setVisible(true);
    }

    public void buildBreadcrumbs() {
        breadcrumbs.clear();
        breadcrumbs.getElement().setInnerHTML("");

        StringBuilder sb = new StringBuilder();
        for (BreadcrumbsItem item : bcCollection.getItems()) {
            sb.append(item.getHtml());
        }

        breadcrumbs.getElement().setInnerHTML(sb.toString());
    }

    public class BreadcrumbsCollection {
        protected ArrayList<BreadcrumbsItem> list = new ArrayList<BreadcrumbsItem>(2);

        public BreadcrumbsCollection() {
            addFirst();
        }

        public BreadcrumbsCollection add(String title, String link) {
            return add(title, link, false);
        }

        public BreadcrumbsCollection add(String title, String link, boolean isActive) {
            BreadcrumbsItem item = new BreadcrumbsItem(title, link, isActive);
            list.add(item);
            return this;
        }

        public void clear() {
            list.clear();
            addFirst();
        }

        protected void addFirst() {
            this.add("Home", "/", false);
        }

        public ArrayList<BreadcrumbsItem> getItems() {
            return list;
        }
    }

    public static class BreadcrumbsItem extends Widget {
        private String title;
        private String link;
        private boolean isActive = false;

        public BreadcrumbsItem(String title, String link) {
            this(title, link, false);
        }

        public BreadcrumbsItem(String title, String link, boolean isActive) {
            super();
            this.title = title;
            this.link = link;
            this.isActive = isActive;
        }

        public String getHtml() {
            String s;
            String escapedTitle = SafeHtmlUtils.htmlEscape(title);
            if (link == null) {
                s = escapedTitle;
            } else {
                s = "<a href=\"" + SafeHtmlUtils.htmlEscape(link) + "\">" + escapedTitle + "</a>";
            }
            return "<li" + (isActive ? " class=\"active\"" : "") + ">" + s + "</li>";
        }
    }
}
