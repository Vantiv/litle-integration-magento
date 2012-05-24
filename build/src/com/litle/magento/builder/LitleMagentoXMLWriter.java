package com.litle.magento.builder;

import java.io.File;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.Attr;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;

public class LitleMagentoXMLWriter {

	protected DocumentBuilderFactory docFactory;
	protected DocumentBuilder docBuilder;
	protected Document doc;
	
	public LitleMagentoXMLWriter() {
		try {
			docFactory = DocumentBuilderFactory.newInstance();
			docBuilder = docFactory.newDocumentBuilder();
			doc = docBuilder.newDocument();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	public boolean generateAndWriteXML(String filePath) {
		try {
			// root element
			Element rootElement = doc.createElement("package");
			doc.appendChild(rootElement);
			
			// root attributes
			setAttribute(rootElement, "xmlns", "http://pear.php.net/dtd/package-2.0");
			setAttribute(rootElement, "xmlns:tasks", "http://pear.php.net/dtd/tasks-1.0");
			setAttribute(rootElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			setAttribute(rootElement, "packagerversion", "1.9.1");
			setAttribute(rootElement, "version", "2.0");
			setAttribute(rootElement, "xsi:schemaLocation", "http://pear.php.net/dtd/tasks-1.0 http://pear.php.net/dtd/tasks-1.0.xsd http://pear.php.net/dtd/package-2.0 http://pear.php.net/dtd/package-2.0.xsd");
			
			// sub elements
			appendChildElement(rootElement, "name", "Litle_Payments");
			appendChildElement(rootElement, "channel", "connect.magentocommerce.com/community");
			appendChildElement(rootElement, "summary", "This extension allows you to accept payments through Litle.");
			appendChildElement(rootElement, "description", "Installation of this extension will allow you to easily accept payments through Litle. Once installed, you can choose to accept credit cards as well as eChecks to be processed by Litle.&#13;\n&#13;\nYou will need to contact Litle to setup a merchant ID prior to processing your transaction. You can test your system against our sandbox without the need to contact Litle first.&#13;\n&#13;\nTo test with sandbox, you may enter any user name and password, and select 'Sandbox' from the drop down menu in Payment Method configuration.&#13;\n&#13;\nWhy Litle?&#13;\n&#13;\nWe deliver the most efficient and effective core processing available to digital and direct merchants. Relevant, value-added solutions help you drive more lasting and profitable customer relationships. We’ll also show you how payments intelligence can power your business and your relationships to greater success. We support you with the best customer experience in the business.&#13; ");
			
			Element leadElement = doc.createElement("lead");
			appendChildElement(leadElement, "name", "Litle");
			appendChildElement(leadElement, "user", "user");
			appendChildElement(leadElement, "sdksupport@litle.com", "email");
			appendChildElement(leadElement, "active", "yes");
			
			// write the content into xml file
			TransformerFactory transformerFactory = TransformerFactory
					.newInstance();
			Transformer transformer = transformerFactory.newTransformer();
			DOMSource source = new DOMSource(doc);
			StreamResult result = new StreamResult(new File(filePath));

			// Output to console for testing
			// StreamResult result = new StreamResult(System.out);

			transformer.transform(source, result);

			System.out.println("File saved!");

		} catch (ParserConfigurationException pce) {
			pce.printStackTrace();
		} catch (TransformerException tfe) {
			tfe.printStackTrace();
		}
		return true;
	}
	
	public void setAttribute(Element in_element, String attrName, String attrVal) {
		Attr attr = doc.createAttribute(attrName);
		attr.setValue(attrVal);
		in_element.setAttributeNode(attr);
	}
	
	public void appendChildElement(Element parentElement, String childName, String childVal) {
		Element elemToAdd = doc.createElement(childName);
		elemToAdd.appendChild(doc.createTextNode(childVal));
		parentElement.appendChild(elemToAdd);
	}

	public static void main(String argv[]) {

	}
}
