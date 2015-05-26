# old2new
Infusionsoft iSDK to PHP-SDK conversion tool
This tool was created to assist in the transition of legacy code (based upon the iSDK) to the new OAuth based PHP SDK.
This tool is not perfect but it will give you a boost in rewriting your code to. It is a very simple (but yet complex) parser
that is based upon regular expressions.

The main file, old2new.php is meant to kick everything off, but you are free to implement how you wish. There are a few options, you can roll your own "signatures" and/or replacement strings to suit your needs. By default, the Parser (/includes/Parser.php) will look into the "includes" directory for signatures that I have taken the time to generate. For most users, this will suffice however, if you wish to convert your code to "another sdk", you can use the signature maker (includes/sigmaker.php) to help you generate a colelction of iSDK methods and convert them over to the proper code. The only cavet with "signatures" is the file must be in proper JSON format.

From there, you can specify if this is to be a "test" run (don't worry it will always generate a backup unless you explicitly run the cdir method, especially after generation), pass true to the test method. This will not modify the orginal files and anything "consumed" will be suffixed as ".consumed".

The parser will automatically copy over the "vendor" folder and contents. If there is another dependancy required by your project, you have two options. If it follows a "PSR" loading pattern, you can modify the included autoload.php to include your dependancy or you can generate a signature and then place the file into the includes/support folder (when copied over, "support" folder is not created) and then call the copySupport method.

I have spent several weeks trying this parser out with sample code I was provided (and it comes without a warranty what so ever) and so far the consumed code seem to come out ratehr well. If for some reason your code des not parse properly, send me a sample (as close to the orginal as possible to include whitespeace, etc) and we'll see if we can figure it out. There are on;y a handful of methods that are reliant of a very specific signature mostly due to the actual method signature (like dsQuery).

In closing, you are free to use and modify this code in accordance to the license. If this code has had a major impact to you, please consider donating what you feel it's worth to http://keepchildrenrockin.org/?page_id=22
