package com.ntobjectives.hackazon.helpdesk.client;

import com.google.gwt.core.client.GWT;
import com.google.gwt.user.client.rpc.RemoteService;
import com.google.gwt.user.client.rpc.RemoteServiceRelativePath;
import com.ntobjectives.hackazon.helpdesk.client.entity.ApplicationData;
import com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry;
import com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage;

import java.util.ArrayList;
import java.util.Date;

@RemoteServiceRelativePath("HelpdeskService")
public interface HelpdeskService extends RemoteService {
    // Sample interface method of remote interface
    String getMessage(String msg);
    Boolean isUserAuthenticated();
    Date getDate();
    ArrayList<Enquiry> getEnquiries();
    ArrayList<Enquiry> getEnquiriesPage(int page);
    ArrayList<Enquiry> getEnquiriesPagePerPage(int page, int perPage);
    ArrayList<EnquiryMessage> getEnquiryMessages(int enquiryId) throws IllegalArgumentException;
    Enquiry createEnquiry(Enquiry enquiry) throws IllegalArgumentException;
    Enquiry getEnquiryById(int id);
    EnquiryMessage createEnquiryMessage(EnquiryMessage message) throws IllegalArgumentException;
    ApplicationData getAppData();

    /**
     * Utility/Convenience class.
     * Use HelpdeskService.App.getInstance() to access static instance of HelpdeskServiceAsync
     */
    public static class App {
        private static HelpdeskServiceAsync ourInstance = GWT.create(HelpdeskService.class);

        public static synchronized HelpdeskServiceAsync getInstance() {
            return ourInstance;
        }
    }
}
