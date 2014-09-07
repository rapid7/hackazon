package com.ntobjectives.hackazon.helpdesk.client.entity;

import com.sun.istack.internal.NotNull;

import java.io.Serializable;
import java.util.Date;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 02.09.2014
 * Time: 15:23
 */
public class EnquiryMessage implements Serializable {
    private int id;

    @NotNull
    private int enquiry_id;

    private int author_id;

    @NotNull
    private String message;
    private Date created_on;
    private Date updated_on;

    private User author;
    private Enquiry enquiry;

    public Date getUpdatedOn() {
        return updated_on;
    }

    public void setUpdatedOn(Date updated_on) {
        this.updated_on = updated_on;
    }

    public Date getCreatedOn() {
        return created_on;
    }

    public void setCreatedOn(Date created_on) {
        this.created_on = created_on;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getAuthorId() {
        return author_id;
    }

    public void setAuthorId(int author_id) {
        this.author_id = author_id;
    }

    public int getEnquiryId() {
        return enquiry_id;
    }

    public void setEnquiryId(int enquiry_id) {
        this.enquiry_id = enquiry_id;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public User getAuthor() {
        return author;
    }

    public String getAuthorName() {
        User author = getAuthor();
        return author == null ? "Unknown" : author.getUsername();
    }

    public void setAuthor(User author) {
        if (author == null) {
            author_id = 0;
        } else {
            author_id = author.getId();
        }
        this.author = author;
    }

    public Enquiry getEnquiry() {
        return enquiry;
    }

    public void setEnquiry(Enquiry enquiry) {
        if (enquiry == null) {
            enquiry_id = 0;
        } else {
            enquiry_id = enquiry.getId();
        }
        this.enquiry = enquiry;
    }
}
