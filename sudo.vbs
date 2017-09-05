Set args=WScript.Arguments
quote = """"

If 0=args.Count Then Set args=Nothing:WScript.Quit
cmds=args.Item(0)

For i=1 To args.Count-1
    If instr(args.Item(i), " ") Then
        para=para + quote + args.Item(i) + quote + " "
    Else
        para=para + args.Item(i) + " "
    End if
Next
'msgbox para
WScript.CreateObject("Shell.application").shellexecute cmds, para,"","runas",1
WScript.Quit