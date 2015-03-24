package com.ntobjectives.hackazon.network;

import com.ntobjectives.hackazon.model.*;
import retrofit.http.*;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:28
 */
public interface Hackazon {
    // ORDERS
    @GET("/api/order")
    Order.OrdersResponse orders(@Query("page") int page, @Query("per_page") int perPage);

    @GET("/api/order/{id}")
    Order order(@Path("id") String id);

    @POST("/api/order")
    Order addOrder(@Body Order order);

    @POST("/api/orderItems")
    OrderItem addOrderItem(@Body OrderItem item);

    // PRODUCTS
    @GET("/api/product")
    Product.ProductsResponse products();

    @GET("/api/product")
    Product.ProductsResponse products(@Query("page") int page);

    @GET("/api/product")
    Product.ProductsResponse products(@Query("page") int page, @Query("categoryID") int categoryID);

    @GET("/api/product/{id}")
    Product product(@Path("id") String id);

    // CATEGORIES
    @GET("/api/category")
    Category.CategoriesResponse categories(@Query("page") int page, @Query("per_page") int per_page);

    // CART
    @GET("/api/cart/my")
    Cart myCart();

    @PUT("/api/cart/{id}")
    Cart updateCart(@Path("id") int id, @Body Cart cart);

    @DELETE("/api/cart/{id}")
    Void deleteCart(@Path("id") int id);

    @GET("/api/cart/my")
    Cart myCart(@Query("uid") String uid);

    // CART ITEMS
    @POST("/api/cartItems")
    CartItem addCartItem(@Body CartItem item);

    @PUT("/api/cartItems/{id}")
    CartItem updateCartItem(@Path("id") int id, @Body CartItem item);

    @DELETE("/api/cartItems/{id}")
    Void deleteCartItem(@Path("id") int id);

    // USER
    @GET("/api/user/me")
    User me();

    @PUT("/api/user/{id}")
    User updateUser(@Path("id") int id, @Body User user);

    // CUSTOMER ADDRESS
    @GET("/api/customerAddress")
    CustomerAddress.CustomerAddressesResponse customerAddresses();

    @GET("/api/customerAddress")
    CustomerAddress.CustomerAddressesResponse customerAddresses(@Query("page") int page, @Query("per_page") int per_page);

    @GET("/api/customerAddress/{id}")
    CustomerAddress customerAddresses(@Path("id") int id);

    @PUT("/api/customerAddress/{id}")
    CustomerAddress updateCustomerAddresses(@Path("id") int id, @Body CustomerAddress address);

    @POST("/api/customerAddress")
    CustomerAddress addCustomerAddress(@Body CustomerAddress address);

    // ORDER ADDRESS
    @GET("/api/orderAddresses")
    OrderAddress.OrderAddressesResponse orderAddresses();

    @GET("/api/orderAddresses/{id}")
    OrderAddress orderAddress(@Path("id") int id);

    @PUT("/api/orderAddresses/{id}")
    OrderAddress updateOrderAddress(@Path("id") int id, @Body OrderAddress address);

    @POST("/api/orderAddresses")
    OrderAddress addOrderAddress(@Body OrderAddress address);

    // CONTACT MESSAGES
    @GET("/api/contactMessages")
    ContactMessage.ContactMessageResponse contactMessages();

    @GET("/api/contactMessages/{id}")
    ContactMessage contactMessage(@Path("id") int id);

    @PUT("/api/contactMessages/{id}")
    ContactMessage updateContactMessages(@Path("id") int id, @Body ContactMessage contactMessage);

    @POST("/api/contactMessages")
    ContactMessage addContactMessages(@Body ContactMessage contactMessage);
}
