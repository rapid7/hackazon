package com.ntobjectives.hackazon.helpdesk.client.entity;

import com.sun.istack.internal.NotNull;

import java.io.Serializable;
import java.util.Date;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 02.09.2014
 * Time: 14:58
 */
public class Enquiry implements Serializable {
    private int id;
    @NotNull
    private String title;

    @NotNull
    private String description;

    @NotNull
    private String status = "new";

    @NotNull
    private int created_by;
    private int assigned_to;

    private Date created_on = new Date();
    private Date updated_on = new Date();

    private User createdByUser;
    private User assignedToUser;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public int getCreatedBy() {
        return created_by;
    }

    public void setCreatedBy(int created_by) {
        this.created_by = created_by;
    }

    public int getAssignedTo() {
        return assigned_to;
    }

    public void setAssignedTo(int assigned_by) {
        this.assigned_to = assigned_by;
    }

    public Date getCreatedOn() {
        return created_on;
    }

    public void setCreatedOn(Date created_on) {
        this.created_on = created_on;
    }

    public Date getUpdatedOn() {
        return updated_on;
    }

    public void setUpdatedOn(Date updated_on) {
        this.updated_on = updated_on;
    }

    public User getCreatedByUser() {
        return createdByUser;
    }

    public void setCreatedByUser(User createdByUser) {
        if (createdByUser == null) {
            created_by = 0;
        } else {
            created_by = createdByUser.getId();
        }
        this.createdByUser = createdByUser;
    }

    public User getAssignedToUser() {
        return assignedToUser;
    }

    public void setAssignedToUser(User assignedToUser) {
        if (assignedToUser == null) {
            assigned_to = 0;
        } else {
            assigned_to = assignedToUser.getId();
        }
        this.assignedToUser = assignedToUser;
    }
}
