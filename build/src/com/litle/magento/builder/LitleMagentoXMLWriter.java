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

public class LitleMagentoXMLWriter {

	protected DocumentBuilderFactory docFactory;
	protected DocumentBuilder docBuilder;
	protected Document doc;
	
	protected String pathToLitleMagentoIntegrationFolder;
	protected String pathToFolderToSaveIn;
	protected String versionNumber;
	
	public LitleMagentoXMLWriter(String versionNumber, String magentoIntegrationFolderPath, String folderToSaveInPath) {
		try {
			docFactory = DocumentBuilderFactory.newInstance();
			docBuilder = docFactory.newDocumentBuilder();
			doc = docBuilder.newDocument();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		// check if the last character in the path is "/" .. if not, then need to add it.
		if( folderToSaveInPath.substring((folderToSaveInPath.length())-1).compareTo("/") == 0 )
			this.pathToFolderToSaveIn = folderToSaveInPath;
		else
			this.pathToFolderToSaveIn = folderToSaveInPath + "/";
		
		this.pathToLitleMagentoIntegrationFolder = magentoIntegrationFolderPath;
		this.versionNumber = versionNumber;
	}

	public boolean generateAndWriteXML() {
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
			appendChildElement(leadElement, "email", "sdksupport@litle.com");
			appendChildElement(leadElement, "active", "yes");
			rootElement.appendChild(leadElement);
			
			// TODO :: populate date and time dynamically
			appendChildElement(rootElement, "date", "2012-05-23");
			appendChildElement(rootElement, "time", "15:35:25");
			
			// TODO :: grab release and api version dynamically
			Element versionElement = doc.createElement("version");
			appendChildElement(versionElement, "release", "8.12.0");
			appendChildElement(versionElement, "api", "8.12.0");
			rootElement.appendChild(versionElement);
			
			Element stabilityElement = doc.createElement("stability");
			appendChildElement(stabilityElement, "release", "stable");
			appendChildElement(stabilityElement, "api", "stable");
			rootElement.appendChild(stabilityElement);
			
			appendChildElement(rootElement, "license", "MIT");
			appendChildElement(rootElement, "notes", "This extension implements Litle XML version 8.12&#13;\n&#13;\nAdditional features include enhanced reporting on orders, transactions, and customers.");
			
			
			
			// write the content into xml file
			TransformerFactory transformerFactory = TransformerFactory
					.newInstance();
			Transformer transformer = transformerFactory.newTransformer();
			DOMSource source = new DOMSource(doc);
			//StreamResult result = new StreamResult(new File(this.pathToFolderToSaveIn + "package.xml"));

			// Output to console for testing
			StreamResult result = new StreamResult(System.out);

			transformer.transform(source, result);

			System.out.println("File saved!");

		//} catch (ParserConfigurationException pce) {
		//	pce.printStackTrace();
		} catch (TransformerException tfe) {
			tfe.printStackTrace();
		}
		return true;
	}
	
	public boolean addNodesFromFileStructureInFolder(Element parentElement, String folderName){
		boolean retVal = false;
		File dir = new File(this.pathToLitleMagentoIntegrationFolder + folderName);
		if( dir.exists() ) {
			File[] children = dir.listFiles();
			if( children != null ) {
				for( int i = 0; i < children.length; i++ ) {
					File fileOrDir = children[i];
					if( fileOrDir != null && fileOrDir.isDirectory() ) {
						addNodesFromFileStructureInFolder(parentElement, folderName + fileOrDir.getName());
					}
				}
			}
		}
		else
			return retVal;
		
		return retVal;
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

	// argv[0] == version number
	// version number for notes is derived from this.
	// argv[1] == path to copy/write the files to.
	// argv[2] == path to litle-integration-magento folder
	public static void main(String argv[]) {
		String versionNumber = "", pathToCopyFiles = "", pathToMagentoIntegration = "";
		
		if(argv.length > 0)
		{
			versionNumber = argv[0];
			pathToCopyFiles = argv[1];
			pathToMagentoIntegration = argv[2];			
		}
		else
		{
			System.out.println("Insufficient number of arguments. This build utility requires 3 arguments to work correctly: Version number, Path to a folder to copy/write files to, Path to litle-integration-magento folder.");
			return;
		}
		
		System.out.println("Values being used:");
		System.out.println("\tVersion Number: " + versionNumber);
		System.out.println("\tPath to Copy Files to: " + pathToCopyFiles);
		System.out.println("\tPath to litle-integration-magento: " + pathToMagentoIntegration);
		
//		if( versionNumber.isEmpty() )
//			versionNumber = "8.12.0";
//		if( pathToCopyFiles.isEmpty() )
//			pathToCopyFiles = "/usr/local/litle-home/aagarwal/MagentoBuild/";
//		if( pathToMagentoIntegration.isEmpty() )
//			pathToMagentoIntegration = "/usr/local/litle-home/aagarwal/git/litle-integration-magento/";
		
		LitleMagentoXMLWriter newObj = new LitleMagentoXMLWriter(versionNumber, pathToCopyFiles, pathToMagentoIntegration);
		newObj.generateAndWriteXML();
	}
}
