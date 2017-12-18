pragma solidity ^0.4.12;
import './ifowned.sol';
import './iferc20token.sol';

contract IFSmartToken is IFOwned, IFERC20Token {
    function disableTransfers(bool _disable) public;
    function issue(address _to, uint256 _amount) public;
    function destroy(address _from, uint256 _amount) public;
}