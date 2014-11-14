package com.ntobjectives.hackazon.model;

import java.util.HashMap;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 31.10.2014
 * Time: 12:49
 */
public class Countries {
    public static final String US = "US";
    public static final String RU = "RU";

    private static final HashMap<String, String> labels = new HashMap<String, String>();
    static {
        labels.put(US, "United States");
        labels.put(RU, "Russia");
    }

    public static String getLabel(String country) {
        if (!labels.containsKey(country)) {
            return "";
            //throw new IllegalArgumentException("Given country '" + country + "' doesn't exist");
        }
        return labels.get(country);
    }

    public static HashMap<String, String> getLabels() {
        return labels;
    }
}
