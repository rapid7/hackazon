package com.ntobjectives.hackazon.helpdesk.server;

import com.google.gwt.user.server.rpc.RemoteServiceServlet;
import com.ntobjectives.hackazon.helpdesk.client.HelpdeskService;
import com.ntobjectives.hackazon.helpdesk.client.entity.ApplicationData;
import com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry;
import com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage;
import com.ntobjectives.hackazon.helpdesk.client.entity.User;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.HashMap;

public class HelpdeskServiceImpl extends RemoteServiceServlet implements HelpdeskService {
    protected HashMap<Integer, Enquiry> enquiries = new HashMap<Integer, Enquiry>(0);
    int enquiryCounter = 0;
    protected HashMap<Integer, EnquiryMessage> enquiryMessages = new HashMap<Integer, EnquiryMessage>(0);
    int enquiryMessageCounter = 0;

    protected User currentUser;
    protected User admin;

    public HelpdeskServiceImpl() {
        super();
        currentUser = new User();
        currentUser.setUsername("test_user");
        currentUser.setFirstName("Вася");
        currentUser.setId(1);

        admin = new User();
        admin.setUsername("admin");
        admin.setFirstName("Administrator");
        admin.setId(2);

        prepareData();
    }

    // Implementation of sample interface method
    public String getMessage(String msg) {
        return "Browser said: \"" + msg + "\"<br>Server answered: \"Hi!\"";
    }

    @Override
    public Boolean isUserAuthenticated() {
        return true;
    }

    @Override
    public Date getDate() {
        return new Date();
    }

    @Override
    public ArrayList<Enquiry> getEnquiries() {
        return getEnquiriesPage(1);
    }

    @Override
    public ArrayList<Enquiry> getEnquiriesPage(int page) {
        return getEnquiriesPagePerPage(1, 10);
    }

    @Override
    public ArrayList<Enquiry> getEnquiriesPagePerPage(int page, int perPage) {
        ArrayList<Enquiry> list = new ArrayList<Enquiry>(perPage);
        for (Enquiry e : enquiries.values()) {
            list.add(e);
        }
        return list;
    }

    @Override
    public ArrayList<EnquiryMessage> getEnquiryMessages(int enquiryId) throws IllegalArgumentException {
        if (!enquiries.containsKey(enquiryId)) {
            throw new IllegalArgumentException("No such an enquiry with id=" + enquiryId + "!");
        }

        ArrayList<EnquiryMessage> list = new ArrayList<EnquiryMessage>(0);
        Object[] keys = enquiryMessages.keySet().toArray();
        Arrays.sort(keys);

        for (Object key : keys) {
            EnquiryMessage m = enquiryMessages.get( key);
            if (m.getEnquiryId() == enquiryId) {
                list.add(m);
            }
        }

        return list;
    }

    @Override
    public Enquiry createEnquiry(Enquiry enquiry) throws IllegalArgumentException {
        if (enquiry == null || enquiry.getTitle() == null || enquiry.getTitle().equals("")
                || enquiry.getDescription() == null || enquiry.getDescription().equals("")
        ) {
            throw new IllegalArgumentException("Errors!!!");
        }
        enquiry.setId(++enquiryCounter);
        enquiry.setCreatedBy(1);
        enquiry.setStatus("new");
        enquiry.setUpdatedOn(new Date());
        enquiry.setCreatedOn(new Date());
        enquiry.setAssignedTo(2);
        enquiries.put(enquiry.getId(), enquiry);
        return enquiry;
    }

    @Override
    public Enquiry getEnquiryById(int id) {
        if (enquiries.containsKey(id)) {
            return enquiries.get(id);
        }
        return null;
    }

    @Override
    public EnquiryMessage createEnquiryMessage(EnquiryMessage message) throws IllegalArgumentException {
        if (message == null || message.getMessage() == null || message.getMessage().equals("")
            || !enquiries.containsKey(message.getEnquiryId())
        ) {
            throw new IllegalArgumentException("Errors!!!");
        }
        message.setId(++enquiryMessageCounter);
        message.setAuthorId(1);
        message.setAuthor(currentUser);

        if (message.getCreatedOn() == null) {
            message.setUpdatedOn(new Date());
            message.setCreatedOn(new Date());
        }
        enquiryMessages.put(message.getId(), message);
        return message;
    }

    @Override
    public ApplicationData getAppData() {
        ApplicationData appData = new ApplicationData();
        appData.setUser(getCurrentUser());
        appData.setAutorized(true);
        return appData;
    }

    public User getCurrentUser() {
        return currentUser;
    }

    protected void prepareData() {
        try {
            Enquiry e = new Enquiry();
            e.setTitle("Hello world");
            e.setDescription("Description 1");
            e.setStatus("closed");
            createEnquiry(e);

            e = new Enquiry();
            e.setTitle("I have a problem");
            e.setDescription("Description 2");
            e.setStatus("rejected");
            createEnquiry(e);
            e.setStatus("rejected");

            e = new Enquiry();
            e.setTitle("What to do if there is a problem?");
            e.setDescription("Description 3");
            createEnquiry(e);
            e.setStatus("resolved");

            // Messages
            EnquiryMessage m = new EnquiryMessage();
            m.setEnquiry(enquiries.get(1));
            m.setMessage("Test message 1");
            createEnquiryMessage(m);

            m = new EnquiryMessage();
            m.setEnquiry(enquiries.get(1));
            m.setMessage("Test message 2");
            createEnquiryMessage(m);

            m = new EnquiryMessage();
            m.setEnquiry(enquiries.get(1));
            m.setMessage("Admin answered message 1");
            createEnquiryMessage(m);
            m.setAuthor(admin);

            m = new EnquiryMessage();
            m.setEnquiry(enquiries.get(1));
            m.setMessage("Test message 3");
            createEnquiryMessage(m);

        } catch (IllegalArgumentException ignored) {
        }
    }
}