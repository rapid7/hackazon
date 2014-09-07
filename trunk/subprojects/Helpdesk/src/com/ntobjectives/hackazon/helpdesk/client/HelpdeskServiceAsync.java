package com.ntobjectives.hackazon.helpdesk.client;

import com.google.gwt.user.client.rpc.AsyncCallback;
import com.ntobjectives.hackazon.helpdesk.client.entity.ApplicationData;
import com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry;
import com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage;

import java.util.ArrayList;
import java.util.Date;

public interface HelpdeskServiceAsync {
    void getMessage(String msg, AsyncCallback<String> async);

    void isUserAuthenticated(AsyncCallback<Boolean> async);

    void getDate(AsyncCallback<Date> async);

    void getEnquiriesPagePerPage(int page, int perPage, AsyncCallback<ArrayList<Enquiry>> async);

    void getEnquiriesPage(int page, AsyncCallback<ArrayList<Enquiry>> async);

    void getEnquiries(AsyncCallback<ArrayList<Enquiry>> async);

    void createEnquiry(Enquiry enquiry, AsyncCallback<Enquiry> async);

    void getEnquiryById(int id, AsyncCallback<Enquiry> async);

    void createEnquiryMessage(EnquiryMessage message, AsyncCallback<EnquiryMessage> async);

    void getEnquiryMessages(int enquiryId, AsyncCallback<ArrayList<EnquiryMessage>> async);

    void getAppData(AsyncCallback<ApplicationData> async);
}
