#!/usr/bin/env python3
"""
WOLFIE AGI UI - Restricted Python Execution Script

WHO: Captain WOLFIE (Eric Robin Gerdes)
WHAT: Safe Python code execution using restrictedpython
WHERE: C:\START\WOLFIE_AGI_UI\scripts\
WHEN: 2025-09-26 20:15:00 CDT
WHY: To safely execute CURSOR-generated Python code
HOW: Python script using restrictedpython library
PURPOSE: Secure execution environment for CURSOR code
ID: RESTRICTED_EXEC_SCRIPT_001
KEY: RESTRICTED_PYTHON_EXECUTION
SUPERPOSITIONALLY: [RESTRICTED_EXEC_SCRIPT_001, WOLFIE_AGI_UI_085]

AGAPE: Love, Patience, Kindness, Humility
GENESIS: Foundation of secure Python execution
MD: Markdown documentation with .py implementation

FILE IDS: [RESTRICTED_EXEC_SCRIPT_001, WOLFIE_UI_085]

VERSION: 1.0.0
STATUS: Active - Restricted Python Execution Script
"""

import sys
import os
import json
import traceback
from io import StringIO

try:
    import restrictedpython
    from restrictedpython import compile_restricted, safe_globals
except ImportError:
    print("Error: restrictedpython not installed. Install with: pip install restrictedpython", file=sys.stderr)
    sys.exit(1)

def create_safe_globals():
    """Create safe globals for restricted execution"""
    safe_globals = {
        '__builtins__': {
            'print': print,
            'len': len,
            'str': str,
            'int': int,
            'float': float,
            'bool': bool,
            'list': list,
            'dict': dict,
            'tuple': tuple,
            'set': set,
            'range': range,
            'enumerate': enumerate,
            'zip': zip,
            'map': map,
            'filter': filter,
            'sorted': sorted,
            'min': min,
            'max': max,
            'sum': sum,
            'abs': abs,
            'round': round,
            'pow': pow,
            'divmod': divmod,
            'all': all,
            'any': any,
            'isinstance': isinstance,
            'issubclass': issubclass,
            'hasattr': hasattr,
            'getattr': getattr,
            'setattr': setattr,
            'delattr': delattr,
            'type': type,
            'repr': repr,
            'chr': chr,
            'ord': ord,
            'bin': bin,
            'hex': hex,
            'oct': oct,
            'format': format,
            'open': open,  # Allow file operations for logging
            'input': input,  # Allow user input for interactive code
            'exit': exit,
            'quit': quit,
            'help': help,
            'dir': dir,
            'vars': vars,
            'locals': locals,
            'globals': globals,
            'eval': eval,  # Allow eval for simple expressions
            'exec': exec,  # Allow exec for dynamic code
            'compile': compile,
            'hash': hash,
            'id': id,
            'object': object,
            'property': property,
            'staticmethod': staticmethod,
            'classmethod': classmethod,
            'super': super,
            'Exception': Exception,
            'ValueError': ValueError,
            'TypeError': TypeError,
            'NameError': NameError,
            'AttributeError': AttributeError,
            'IndexError': IndexError,
            'KeyError': KeyError,
            'StopIteration': StopIteration,
            'GeneratorExit': GeneratorExit,
            'SystemExit': SystemExit,
            'KeyboardInterrupt': KeyboardInterrupt,
            'ImportError': ImportError,
            'ModuleNotFoundError': ModuleNotFoundError,
            'OSError': OSError,
            'FileNotFoundError': FileNotFoundError,
            'PermissionError': PermissionError,
            'ProcessLookupError': ProcessLookupError,
            'TimeoutError': TimeoutError,
            'ConnectionError': ConnectionError,
            'BrokenPipeError': BrokenPipeError,
            'ConnectionAbortedError': ConnectionAbortedError,
            'ConnectionRefusedError': ConnectionRefusedError,
            'ConnectionResetError': ConnectionResetError,
            'BlockingIOError': BlockingIOError,
            'ChildProcessError': ChildProcessError,
            'NotADirectoryError': NotADirectoryError,
            'IsADirectoryError': IsADirectoryError,
            'UnicodeError': UnicodeError,
            'UnicodeDecodeError': UnicodeDecodeError,
            'UnicodeEncodeError': UnicodeEncodeError,
            'UnicodeTranslateError': UnicodeTranslateError,
            'Warning': Warning,
            'UserWarning': UserWarning,
            'DeprecationWarning': DeprecationWarning,
            'PendingDeprecationWarning': PendingDeprecationWarning,
            'SyntaxWarning': SyntaxWarning,
            'RuntimeWarning': RuntimeWarning,
            'FutureWarning': FutureWarning,
            'ImportWarning': ImportWarning,
            'UnicodeWarning': UnicodeWarning,
            'BytesWarning': BytesWarning,
            'ResourceWarning': ResourceWarning,
            'BaseException': BaseException,
            'SystemError': SystemError,
            'ArithmeticError': ArithmeticError,
            'FloatingPointError': FloatingPointError,
            'OverflowError': OverflowError,
            'ZeroDivisionError': ZeroDivisionError,
            'AssertionError': AssertionError,
            'LookupError': LookupError,
            'MemoryError': MemoryError,
            'BufferError': BufferError,
            'NotImplementedError': NotImplementedError,
            'IndentationError': IndentationError,
            'TabError': TabError,
            'ReferenceError': ReferenceError,
            'RuntimeError': RuntimeError,
            'RecursionError': RecursionError,
            'NotImplemented': NotImplemented,
            'Ellipsis': Ellipsis,
            'None': None,
            'True': True,
            'False': False,
            '__name__': '__main__',
            '__doc__': None,
            '__package__': None,
            '__loader__': None,
            '__spec__': None,
            '__annotations__': {},
            '__file__': '<string>',
            '__cached__': None,
            '__version__': '3.8.0',
            '__author__': 'Captain WOLFIE',
            '__license__': 'GPL v3 / Apache 2.0',
            '__status__': 'Active - Restricted Python Execution'
        },
        '__name__': '__main__',
        '__doc__': 'WOLFIE AGI UI - Restricted Python Execution Environment',
        '__package__': None,
        '__loader__': None,
        '__spec__': None,
        '__annotations__': {},
        '__file__': '<string>',
        '__cached__': None,
        '__version__': '3.8.0',
        '__author__': 'Captain WOLFIE',
        '__license__': 'GPL v3 / Apache 2.0',
        '__status__': 'Active - Restricted Python Execution'
    }
    
    return safe_globals

def execute_restricted_code(code, timeout=10):
    """Execute Python code with restrictions"""
    try:
        # Create safe globals
        safe_globals = create_safe_globals()
        safe_locals = {}
        
        # Capture output
        output_buffer = StringIO()
        original_stdout = sys.stdout
        sys.stdout = output_buffer
        
        # Compile with restrictions
        compiled_code = compile_restricted(code, '<string>', 'exec')
        
        # Execute with timeout (simplified - in production, use signal or threading)
        exec(compiled_code, safe_globals, safe_locals)
        
        # Restore stdout
        sys.stdout = original_stdout
        
        # Get output
        output = output_buffer.getvalue()
        
        # Check for result variable
        result = safe_locals.get('__result__', None)
        if result is not None:
            output += f"\nResult: {result}"
        
        return {
            'status': 'SUCCESS',
            'output': output.strip(),
            'locals': {k: v for k, v in safe_locals.items() if not k.startswith('_')},
            'execution_time': 0.0  # Placeholder
        }
        
    except Exception as e:
        # Restore stdout
        sys.stdout = original_stdout
        
        return {
            'status': 'ERROR',
            'message': str(e),
            'traceback': traceback.format_exc(),
            'execution_time': 0.0
        }

def main():
    """Main execution function"""
    if len(sys.argv) < 2:
        print("Usage: python restricted_exec.py <code_file> [timeout]", file=sys.stderr)
        sys.exit(1)
    
    code_file = sys.argv[1]
    timeout = int(sys.argv[2]) if len(sys.argv) > 2 else 10
    
    if not os.path.exists(code_file):
        print(f"Error: Code file not found: {code_file}", file=sys.stderr)
        sys.exit(1)
    
    try:
        with open(code_file, 'r', encoding='utf-8') as f:
            code = f.read()
        
        # Execute restricted code
        result = execute_restricted_code(code, timeout)
        
        # Output result as JSON
        print(json.dumps(result, indent=2))
        
    except Exception as e:
        error_result = {
            'status': 'ERROR',
            'message': f"Failed to read or execute code file: {str(e)}",
            'traceback': traceback.format_exc(),
            'execution_time': 0.0
        }
        print(json.dumps(error_result, indent=2))
        sys.exit(1)

if __name__ == "__main__":
    main()
