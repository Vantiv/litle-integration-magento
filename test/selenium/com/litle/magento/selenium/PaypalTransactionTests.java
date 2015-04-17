package com.litle.magento.selenium;

import org.junit.Before;
import org.junit.Test;

public class PaypalTransactionTests extends BaseTestCase{
    @Before
    public void setup() throws Exception {
        iAmDoingLPaypalTransaction();
    }
    
    @Test
    public void doASucessfulAuthWithOnepage() throws Exception {
        iAmDoingLPaypalAuth();
        
        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();
    }
    
    @Test
    public void doASucessfulSaleWithOnepage() throws Exception {
        iAmDoingLPaypalSale();
        
        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();
    }
    
    @Test
    public void doASucessfulAuthInCart() throws Exception {
        iAmDoingLPaypalAuth();
        
        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutInCartWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();
    }
}
