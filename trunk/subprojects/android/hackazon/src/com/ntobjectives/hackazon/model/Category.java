package com.ntobjectives.hackazon.model;

import android.annotation.SuppressLint;
import android.support.annotation.NonNull;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:29
 */
public class Category {
    public int categoryID;
    public String name;
    public Integer parent;
    public Integer products_count;
    public String description;
    public String picture;
    public Integer products_count_admin;
    public String about;
    public String enabled;
    public String meta_title;
    public String meta_keywords;
    public String meta_desc;
    public String hurl;
    public String canonical;
    public String h1;
    public int hidden = 0;
    public int lpos = 0;
    public int rpos = 0;
    public int depth = 0;

    @SuppressWarnings("serial")
    public static class List extends ArrayList<Category> {
    }

    public static class CategoriesResponse extends CollectionResponse<List> {
    }

    public static class Node implements Comparable {
        protected Category category;
        protected ArrayList<Node> children = new ArrayList<Node>();

        public Node(Category category) {
            this.category = category;
        }

        public Category getCategory() {
            return category;
        }

        public List flatten() {
            List result = new List();
            result.add(category);

            if (children.size() > 0) {
                Object[] forSort = children.toArray();
                Arrays.sort(forSort);

                for (Object ob : forSort) {
                    Node node = (Node) ob;
                    result.addAll(node.flatten());
                }
            }
            return result;
        }

        @Override
        public int compareTo(@NonNull Object another) {
            Node an = (Node) another;
            return category.name.compareTo(an.getCategory().name);
        }
    }

    public static class TreeBuilder {
        protected HashMap<Integer, Node> map;
        protected List list;

        public TreeBuilder(List list) {
            this.list = list;
        }

        @SuppressLint("UseSparseArrays")
        public List build() {
            map = new HashMap<Integer, Node>(list.size());

            for (Category cat : list) {
                map.put(cat.categoryID, new Node(cat));
            }

            for (Node node : map.values()) {
                if (node.getCategory().parent != 0) {
                    Node parent = map.get(node.getCategory().parent);
                    if (parent != null) {
                        parent.children.add(node);
                    }
                }
            }

            return map.get(1) != null ? map.get(1).flatten() : new List();
        }
    }
}
