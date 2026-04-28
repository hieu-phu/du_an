import urllib.parse

def fix_encoding(text):
    try:
        # Try to encode as cp1252 and decode as utf-8
        return text.encode('cp1252').decode('utf-8')
    except Exception as e:
        return text

text = "DÃ¡Â»Â± ÃƒÂ¡n"
print("Original:", text)
print("Fix 1:", fix_encoding(text))
print("Fix 2:", fix_encoding(fix_encoding(text)))

# Let's also try utf-8 -> latin1 -> utf-8
def fix_encoding_latin1(text):
    try:
        return text.encode('latin1').decode('utf-8')
    except Exception as e:
        return text
print("Fix latin1 1:", fix_encoding_latin1(text))
print("Fix latin1 2:", fix_encoding_latin1(fix_encoding_latin1(text)))
