package com.litle.magento.builder;

import java.io.File;
import java.io.FileInputStream;
import java.io.InputStream;
import java.security.MessageDigest;
import java.text.SimpleDateFormat;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import java.util.Date;
import java.util.HashMap;

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
	
	protected HashMap<String, String> folderToRoleMap;

	public LitleMagentoXMLWriter(String versionNumber,
			String folderToSaveInPath, String magentoIntegrationFolderPath) {
		try {
			docFactory = DocumentBuilderFactory.newInstance();
			docBuilder = docFactory.newDocumentBuilder();
			doc = docBuilder.newDocument();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		// check if the last character in the path is "/" .. if not, then need
		// to add it.
		if (folderToSaveInPath.substring((folderToSaveInPath.length()) - 1)
				.compareTo("/") == 0)
			this.pathToFolderToSaveIn = folderToSaveInPath;
		else
			this.pathToFolderToSaveIn = folderToSaveInPath + "/";

		this.pathToLitleMagentoIntegrationFolder = magentoIntegrationFolderPath;
		this.versionNumber = versionNumber;
		
		this.folderToRoleMap = new HashMap<String, String>();
		this.folderToRoleMap.put("Litle", "magelocal");
		this.folderToRoleMap.put("design", "magedesign");
		this.folderToRoleMap.put("etc","mageetc");
	}

	public boolean generateAndWriteXML() {
		try {
			// root element
			Element rootElement = doc.createElement("package");
			doc.appendChild(rootElement);

		    String packageName = "Litle_Payments";
		    String packageVersion = "8.13.0";
		    String packageStability = "stable";
		    String packageLicense = "MIT";
		    String packageChannel = "community";
		    String packageExtends = "";
		    String packageSummary = "This extension allows you to accept payments through Litle.";
		    String packageDescription = "Installation of this extension will allow you to easily accept payments through Litle. Once installed, you can choose to accept credit cards as well as eChecks to be processed by Litle.&#13;\n&#13;\nYou will need to contact Litle to setup a merchant ID prior to processing your transaction. You can test your system against our sandbox without the need to contact Litle first.&#13;\n&#13;\nTo test with sandbox, you may enter any user name and password, and select 'Sandbox' from the drop down menu in Payment Method configuration.&#13;\n&#13;\nWhy Litle?&#13;\n&#13;\nWe deliver the most efficient and effective core processing available to digital and direct merchants. Relevant, value-added solutions help you drive more lasting and profitable customer relationships. We’ll also show you how payments intelligence can power your business and your relationships to greater success. We support you with the best customer experience in the business.&#13; ";
		    String packageNotes = "This extension implements Litle XML version 8.12&#13;\n&#13;\nAdditional features include enhanced reporting on orders, transactions, and customers.";
		    
		    String authorName = "Litle";
			String authorUser = "Litle";
			String authorEmail = "sdksupport@litle.com";
			
			String packageCompatible = "";
			String phpMinElement = "5.2.0";
			String phpMaxElement = "6.0.0";
		    
			// sub elements
			appendChildElement(rootElement, "name", packageName);
			appendChildElement(rootElement, "version", packageVersion);
			appendChildElement(rootElement, "stability", packageStability);
			appendChildElement(rootElement, "license", packageLicense);
			appendChildElement(rootElement, "channel", packageChannel);
			appendChildElement(rootElement, "extends", packageExtends);
			appendChildElement(rootElement, "summary", packageSummary);
			appendChildElement(rootElement,"description",packageDescription);
			appendChildElement(rootElement,"notes",packageNotes);
		
			Element authorsElement = doc.createElement("authors");
			Element authorElement = doc.createElement("author");
			appendChildElement(authorElement, "name", authorName);
			appendChildElement(authorElement, "user", authorUser);
			appendChildElement(authorElement, "email", authorEmail);
			authorsElement.appendChild(authorElement);
			rootElement.appendChild(authorsElement);
			
			SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
			SimpleDateFormat timeFormat = new SimpleDateFormat("HH:mm:ss");
			appendChildElement(rootElement, "date", dateFormat.format(new Date()));
			appendChildElement(rootElement, "time", timeFormat.format(new Date()));

			Element contentsElement = doc.createElement("contents");
			Element localTargetElement = doc.createElement("target");
			setAttribute(localTargetElement, "name", "magelocal");
			Element dirLitleElement = doc.createElement("dir");
			setAttribute(dirLitleElement, "name", "Litle");
			Element emptyDirElement = doc.createElement("dir");
			
			addNodesFromFileStructureInFolder(emptyDirElement, "app/code/local/Litle/");
			
			dirLitleElement.appendChild(emptyDirElement);
			localTargetElement.appendChild(dirLitleElement);
			contentsElement.appendChild(localTargetElement);

			Element etcTargetElement = doc.createElement("target");
			setAttribute(etcTargetElement, "name", "mageetc");
			addNodesFromFileStructureInFolder(etcTargetElement, "app/etc/");
			contentsElement.appendChild(etcTargetElement);
		
			Element designTargetElement = doc.createElement("target");
			setAttribute(designTargetElement, "name", "magedesign");
			addNodesFromFileStructureInFolder(designTargetElement, "app/design/");
			contentsElement.appendChild(designTargetElement);
			
			rootElement.appendChild(contentsElement);
			
			appendChildElement(rootElement, "compatible", packageCompatible);
			
			Element dependenciesElement = doc.createElement("dependencies");
			Element requiredElement = doc.createElement("required");
			Element phpElement = doc.createElement("php");
			appendChildElement(phpElement, "min", phpMinElement);
			appendChildElement(phpElement, "max", phpMaxElement);
			requiredElement.appendChild(phpElement);
			dependenciesElement.appendChild(requiredElement);
			rootElement.appendChild(dependenciesElement);
			
			// write the content into xml file
			TransformerFactory transformerFactory = TransformerFactory
					.newInstance();
			Transformer transformer = transformerFactory.newTransformer();
			DOMSource source = new DOMSource(doc);
			// StreamResult result = new StreamResult(new
			// File(this.pathToFolderToSaveIn + "package.xml"));

			// Output to console for testing
			StreamResult result = new StreamResult(System.out);

			transformer.transform(source, result);

			//System.out.println("File saved!");

			// } catch (ParserConfigurationException pce) {
			// pce.printStackTrace();
		} catch (TransformerException tfe) {
			tfe.printStackTrace();
		}
		return true;
	}

	public boolean addNodesFromFileStructureInFolder(Element parentElement, String folderName) {
		boolean retVal = false;
		File dir = new File(this.pathToLitleMagentoIntegrationFolder + folderName);
		if (dir.exists()) {
			File[] children = dir.listFiles();
			if (children != null) {
				for (int i = 0; i < children.length; i++) {
					File fileOrDir = children[i];
					if (fileOrDir != null && fileOrDir.isDirectory()) {
						Element dirElement = doc.createElement("dir");
						setAttribute(dirElement, "name", fileOrDir.getName());
						boolean wasSomethingAdded = addNodesFromFileStructureInFolder(dirElement,(folderName + "/" + fileOrDir.getName()));
						if( wasSomethingAdded ){
							parentElement.appendChild(dirElement);
							retVal = true;
						}
					} else {
						try {
							Element fileElement = doc.createElement("file");
							// file attributes
							setAttribute(fileElement, "name", fileOrDir.getName());
							setAttribute(fileElement, "hash", getMD5Checksum(fileOrDir.getAbsolutePath()));
							
							// add file to parentElement
							parentElement.appendChild(fileElement);
							retVal = true;
						} catch (Exception e) {
							// TODO Auto-generated catch block
							e.printStackTrace();
						}
					}
				}
			}
		} else
			return retVal;

		return retVal;
	}

	/**************************************
	 * Adds attributes to an existing element
	 **************************************/
	public void setAttribute(Element in_element, String attrName, String attrVal) {
		Attr attr = doc.createAttribute(attrName);
		attr.setValue(attrVal);
		in_element.setAttributeNode(attr);
	}

	/**************************************
	 * Adds child element to a existing element
	 **************************************/
	public void appendChildElement(Element parentElement, String childName,
			String childVal) {
		Element elemToAdd = doc.createElement(childName);
		elemToAdd.appendChild(doc.createTextNode(childVal));
		parentElement.appendChild(elemToAdd);
	}

	/**************************************
	 * Methods to generate the md5CheckSum
	 **************************************/
	public byte[] createChecksum(String filename) throws Exception {
		InputStream fis = new FileInputStream(filename);

		byte[] buffer = new byte[1024];
		MessageDigest complete = MessageDigest.getInstance("MD5");
		int numRead;

		do {
			numRead = fis.read(buffer);
			if (numRead > 0) {
				complete.update(buffer, 0, numRead);
			}
		} while (numRead != -1);

		fis.close();
		return complete.digest();
	}

	// see this How-to for a faster way to convert
	// a byte array to a HEX string
	public String getMD5Checksum(String filename) throws Exception {
		byte[] b = createChecksum(filename);
		String result = "";

		for (int i = 0; i < b.length; i++) {
			result += Integer.toString((b[i] & 0xff) + 0x100, 16).substring(1);
		}
		return result;
	}

	/**************************************
	 * Main method for testing purposes
	 **************************************/
	// argv[0] == version number
	// version number for notes is derived from this.
	// argv[1] == path to copy/write the files to.
	// argv[2] == path to litle-integration-magento folder
	public static void main(String argv[]) {
		String versionNumber = "", pathToCopyFiles = "", pathToMagentoIntegration = "";

		if (argv.length > 0) {
			versionNumber = argv[0];
			pathToCopyFiles = argv[1];
			pathToMagentoIntegration = argv[2];
		} else {
			System.out
					.println("Insufficient number of arguments. This build utility requires 3 arguments to work correctly: Version number, Path to a folder to copy/write files to, Path to litle-integration-magento folder.");
			return;
		}

		LitleMagentoXMLWriter newObj = new LitleMagentoXMLWriter(versionNumber,
				pathToCopyFiles, pathToMagentoIntegration);
		newObj.generateAndWriteXML();
	}
}
