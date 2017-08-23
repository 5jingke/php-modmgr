Set args=WScript.Arguments  
quote = """"

If 0=args.Count Then Set args=Nothing:WScript.Quit  
cmds=args.Item(0) 

For i=1 To args.Count-1  
    If args.Item(i) = "--quote--" Then 
        quote = """"
    Elseif args.Item(i) = "--none--" Then
        quote = ""
    Else
        para=para + quote + args.Item(i) + quote + " " 
    End if  
Next  
' msgbox para
WScript.CreateObject("Shell.application").shellexecute cmds, para,"","runas",1  
WScript.Quit